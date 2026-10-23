<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Support\Appearance;
use App\Support\Locales;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingsController extends Controller
{
    /** @var array<string, string> */
    public const TABS = [
        'perfil' => 'Perfil',
        'aparencia' => 'Aparência',
        'privacidade' => 'Privacidade',
        'notificacoes' => 'Notificações',
        'contas' => 'Contas vinculadas',
        'dados' => 'Dados',
    ];

    /** @var list<string> */
    public const PRONOUNS = ['ela/dela', 'ele/dele', 'elu/delu'];

    public function edit(Request $request): View
    {
        $tab = array_key_exists((string) $request->query('aba'), self::TABS) ? (string) $request->query('aba') : 'perfil';
        $user = $request->user();

        $pronouns = (string) $user->pronouns;
        $isStandard = in_array($pronouns, self::PRONOUNS, true);

        return view('settings.edit', [
            'tab' => $tab,
            'tabs' => array_map(fn ($label) => __($label), self::TABS),
            'appearance' => Appearance::for($user),
            'locales' => Locales::SUPPORTED,
            'user' => $user,
            'pronounOptions' => self::PRONOUNS,
            'pronounChoice' => $isStandard ? $pronouns : '',
            'pronounCustom' => $isStandard ? '' : $pronouns,
            'genres' => Game::whereNotNull('genre')->distinct()->orderBy('genre')->pluck('genre'),
            'steamAccount' => (\Illuminate\Support\Facades\Schema::hasTable('steam_accounts') ? \Illuminate\Support\Facades\DB::table('steam_accounts')->where('user_id', $user->id)->first() : null),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->merge(['username' => Str::lower(trim((string) $request->input('username')))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'username' => ['required', 'regex:/^[a-z0-9_]{3,30}$/', Rule::unique('users', 'username')->ignore($user->id)],
            'pronouns_choice' => ['nullable', Rule::in(self::PRONOUNS)],
            'pronouns_custom' => ['nullable', 'string', 'max:40'],
            'status' => ['nullable', 'string', 'max:80'],
            'bio' => ['nullable', 'string', 'max:300'],
            'location' => ['nullable', 'string', 'max:80'],
            'favorite_genre' => ['nullable', 'string', 'max:40'],
            'website' => ['nullable', 'url:http,https', 'max:200'],
            'platforms' => ['sometimes', 'array', 'max:5'],
            'platforms.*' => ['string', Rule::in(array_keys(\App\Support\Platforms::OPTIONS))],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'name.required' => __('Informe seu nome.'),
            'name.max' => __('O nome pode ter no máximo 60 caracteres.'),
            'username.required' => __('Informe um nome de usuário.'),
            'username.regex' => __('Use de 3 a 30 caracteres: letras minúsculas, números e _.'),
            'username.unique' => __('Esse nome de usuário já está em uso.'),
            'bio.max' => __('A bio pode ter no máximo 300 caracteres.'),
            'website.url' => __('Informe um endereço completo, começando com http:// ou https://.'),
            'avatar.image' => __('Envie uma imagem.'),
            'avatar.mimes' => __('Use uma imagem JPG, PNG ou WebP.'),
            'avatar.max' => __('A foto pode ter no máximo 5 MB.'),
            'avatar.uploaded' => __('Não foi possível enviar a foto. Ela pode ser grande demais para o servidor; tente uma imagem menor.'),
        ]);

        $attributes = [
            'name' => $data['name'],
            'username' => $data['username'],
            'pronouns' => filled($data['pronouns_custom'] ?? null) ? trim($data['pronouns_custom']) : ($data['pronouns_choice'] ?? null),
            'status' => $data['status'] ?? null,
            'bio' => $data['bio'] ?? null,
            'location' => $data['location'] ?? null,
            'favorite_genre' => $data['favorite_genre'] ?? null,
            'website' => $data['website'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $attributes['avatar_path'] = $this->storeAvatar($request->file('avatar'));
        } elseif ($request->boolean('remove_avatar') && $user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $attributes['avatar_path'] = null;
        }

        $user->update($attributes);
        $settings = $user->settings ?? [];
        $settings['gaming_platforms'] = array_values(array_unique($data['platforms'] ?? []));
        $settings['platforms_onboarded'] = true;
        $user->settings = $settings; $user->save();

        return redirect()->route('settings.edit', ['aba' => 'perfil'])->with('status', __('Perfil atualizado.'));
    }

    /** Salva os ajustes de aparência (chamado pelo JavaScript a cada mudança). */
    public function updateAppearance(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        $clean = Appearance::sanitize(array_merge(Appearance::for($user), $request->only(array_keys(Appearance::DEFAULTS))));

        $settings = $user->settings ?? [];
        data_set($settings, 'appearance', $clean);
        $user->update(['settings' => $settings]);

        if ($request->wantsJson()) {
            return response()->json($clean);
        }

        return redirect()->route('settings.edit', ['aba' => 'aparencia']);
    }

    public function updatePrivacy(Request $request): RedirectResponse
    {
        $this->saveSettings($request, 'privacy', ['public_profile', 'show_library']);

        return redirect()->route('settings.edit', ['aba' => 'privacidade'])->with('status', __('Privacidade atualizada.'));
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $this->saveSettings($request, 'notifications', ['reactions', 'replies', 'follows', 'news']);

        return redirect()->route('settings.edit', ['aba' => 'notificacoes'])->with('status', __('Preferências salvas.'));
    }

    /** Baixa tudo o que o Lobby guarda sobre a pessoa, em JSON. */
    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();

        $payload = [
            'exportado_em' => now()->toIso8601String(),
            'conta' => $user->only(['name', 'username', 'email', 'pronouns', 'status', 'bio', 'location', 'favorite_genre', 'website', 'created_at']),
            'preferencias' => $user->settings ?? [],
            'publicacoes' => $user->posts()->get(['id', 'title', 'body', 'rating', 'created_at']),
            'topicos' => $user->threads()->get(['id', 'game_id', 'title', 'body', 'votes', 'created_at']),
            'respostas' => $user->forumReplies()->get(['id', 'forum_thread_id', 'body', 'created_at']),
            'biblioteca' => $user->library()->get()->map(fn ($g) => [
                'jogo' => $g->title,
                'status' => $g->pivot->status,
                'nota' => $g->pivot->rating,
                'horas' => $g->pivot->hours_logged,
                'resenha' => $g->pivot->review,
                'favorito' => (bool) $g->pivot->is_favorite,
            ]),
            'seguindo' => $user->following()->pluck('username'),
        ];

        return response()->streamDownload(
            function () use ($payload) {
                echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            },
            'lobby-meus-dados.json',
            ['Content-Type' => 'application/json; charset=UTF-8'],
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(
            ['password' => ['required', 'current_password']],
            ['password.required' => __('Digite sua senha para confirmar.'), 'password.current_password' => __('Senha incorreta.')],
        );

        $user = $request->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    /**
     * Guarda a foto no disco "public". Se a extensão GD existir, recorta em quadrado
     * e reduz para 512 px (fotos de celular têm vários MB); senão guarda o original.
     */
    private function storeAvatar(UploadedFile $file): string
    {
        $disk = Storage::disk('public');

        if (function_exists('imagecreatefromstring') && function_exists('imagejpeg')) {
            try {
                $src = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

                if ($src !== false) {
                    $w = imagesx($src);
                    $h = imagesy($src);
                    $side = min($w, $h);
                    $out = imagecreatetruecolor(512, 512);
                    imagefill($out, 0, 0, imagecolorallocate($out, 255, 255, 255));
                    imagecopyresampled($out, $src, 0, 0, intdiv($w - $side, 2), intdiv($h - $side, 2), 512, 512, $side, $side);

                    ob_start();
                    imagejpeg($out, null, 88);
                    $jpeg = (string) ob_get_clean();

                    $path = 'avatars/'.Str::random(40).'.jpg';
                    $disk->put($path, $jpeg);

                    return $path;
                }
            } catch (\Throwable) {
                // cai no armazenamento simples abaixo
            }
        }

        return $file->store('avatars', 'public');
    }

    /**
     * @param  list<string>  $keys
     */
    private function saveSettings(Request $request, string $group, array $keys): void
    {
        $user = $request->user();
        $settings = $user->settings ?? [];

        foreach ($keys as $key) {
            data_set($settings, "{$group}.{$key}", $request->boolean($key));
        }

        $user->update(['settings' => $settings]);
    }
}
