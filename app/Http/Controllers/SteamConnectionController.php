<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SteamConnectionController extends Controller
{
    private const ENDPOINT = 'https://steamcommunity.com/openid/login';

    public function redirect(Request $request): RedirectResponse
    {
        $nonce = Str::random(48);
        $request->session()->put('steam_link_nonce', $nonce);
        $callback = route('steam.callback', ['state' => $nonce]);

        return redirect()->away(self::ENDPOINT.'?'.http_build_query([
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
            'openid.mode' => 'checkid_setup',
            'openid.return_to' => $callback,
            'openid.realm' => url('/'),
            'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        ], '', '&', PHP_QUERY_RFC3986));
    }

    public function callback(Request $request): RedirectResponse
    {
        $expected = $request->session()->pull('steam_link_nonce');
        if (! is_string($expected) || ! hash_equals($expected, (string) $request->query('state'))) {
            return $this->fail('A solicitação de conexão expirou. Tente novamente.');
        }

        if ($request->query('openid_mode') === 'cancel' || $request->query('openid.mode') === 'cancel') {
            return $this->fail('Conexão com a Steam cancelada.');
        }

        $params = $request->query();
        if (($params['openid_mode'] ?? $params['openid.mode'] ?? null) !== 'id_res') {
            return $this->fail('A Steam não confirmou o login.');
        }

        $returnedTo = (string) ($params['openid_return_to'] ?? $params['openid.return_to'] ?? '');
        $provider = (string) ($params['openid_op_endpoint'] ?? $params['openid.op_endpoint'] ?? '');
        if ($returnedTo !== route('steam.callback', ['state' => $expected])
            || $provider !== self::ENDPOINT) {
            return $this->fail('O retorno da autenticação Steam é inválido.');
        }

        $claimed = (string) ($params['openid_claimed_id'] ?? $params['openid.claimed_id'] ?? '');
        $identity = (string) ($params['openid_identity'] ?? $params['openid.identity'] ?? '');
        if (! preg_match('~^https?://steamcommunity\.com/openid/id/([0-9]{17})$~', $claimed, $matches)
            || $identity !== $claimed) {
            return $this->fail('A identificação recebida da Steam é inválida.');
        }

        // Nunca confiar nos parâmetros do navegador sem a verificação no provedor OpenID.
        $verification = [];
        foreach ($params as $key => $value) {
            $key = str_replace('_', '.', $key);
            if (str_starts_with($key, 'openid.') && is_string($value)) {
                $verification[$key] = $value;
            }
        }
        $verification['openid.mode'] = 'check_authentication';

        try {
            $response = Http::asForm()->timeout(15)->post(self::ENDPOINT, $verification);
            if (! $response->successful() || ! preg_match('/^is_valid:true\s*$/m', $response->body())) {
                return $this->fail('Não foi possível verificar a autenticação com a Steam.');
            }
        } catch (\Throwable $exception) {
            report($exception);
            return $this->fail('A Steam não respondeu. Tente novamente mais tarde.');
        }

        $steamId = $matches[1];
        $userId = $request->user()->getAuthIdentifier();
        try {
            DB::transaction(function () use ($userId, $steamId) {
                $existing = DB::table('steam_accounts')->where('steam_id', $steamId)->first();
                if ($existing && (string) $existing->user_id !== (string) $userId) {
                    throw new \DomainException('Esta conta Steam já está vinculada a outro usuário do Lobby.');
                }
                DB::table('steam_accounts')->updateOrInsert(
                    ['user_id' => $userId],
                    ['steam_id' => $steamId, 'updated_at' => now(), 'created_at' => $existing?->created_at ?? now()]
                );
            });
        } catch (\DomainException $exception) {
            return $this->fail($exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);
            return $this->fail('Não foi possível salvar a conexão Steam. Verifique as migrações.');
        }

        return redirect()->route('settings.edit', ['aba' => 'contas'])->with('status', 'Conta Steam conectada com sucesso.');
    }

    public function disconnect(Request $request): RedirectResponse
    {
        DB::table('steam_accounts')->where('user_id', $request->user()->getAuthIdentifier())->delete();
        return redirect()->route('settings.edit', ['aba' => 'contas'])->with('status', 'Conta Steam desconectada.');
    }

    private function fail(string $message): RedirectResponse
    {
        return redirect()->route('settings.edit', ['aba' => 'contas'])->with('steam_error', $message);
    }
}
