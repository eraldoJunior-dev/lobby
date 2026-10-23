<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Ações da pessoa logada sobre um jogo: status, favorito, horas e avaliação. */
class GameLibraryController extends Controller
{
    /** @var list<string> */
    public const STATUSES = ['zerado', 'jogando', 'backlog'];

    /** Marca como jogado / jogando / quero jogar. Clicar no status atual remove a marca. */
    public function status(Request $request, Game $game): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(self::STATUSES)]]);

        $entry = $this->entry($request, $game);
        $this->save($request, $game, ['status' => $entry?->pivot->status === $data['status'] ? null : $data['status']]);

        return $this->back($game);
    }

    public function favorite(Request $request, Game $game): RedirectResponse
    {
        $entry = $this->entry($request, $game);
        $this->save($request, $game, ['is_favorite' => ! (bool) $entry?->pivot->is_favorite]);

        return $this->back($game);
    }

    /** Soma horas jogadas ao total da pessoa neste jogo. */
    public function hours(Request $request, Game $game): RedirectResponse
    {
        $data = $request->validate(
            ['hours' => ['required', 'integer', 'min:1', 'max:5000']],
            [
                'hours.required' => __('Informe quantas horas você jogou.'),
                'hours.integer' => __('Use um número inteiro de horas.'),
                'hours.min' => __('Use pelo menos 1 hora.'),
                'hours.max' => __('Esse valor é alto demais.'),
            ],
        );

        $total = min(65535, (int) $this->entry($request, $game)?->pivot->hours_logged + (int) $data['hours']);
        $this->save($request, $game, ['hours_logged' => $total]);

        return $this->back($game, __('Horas registradas.'));
    }

    /** Nota (1 a 5) e resenha opcional. Sem nota e sem texto, apaga a avaliação. */
    public function review(Request $request, Game $game): RedirectResponse
    {
        $data = $request->validate(
            [
                'rating' => ['nullable', 'numeric', 'between:0.5,5', 'multiple_of:0.5'],
                'review' => ['nullable', 'string', 'max:2000'],
            ],
            [
                'rating.between' => __('A nota vai de 0,5 a 5 estrelas, em intervalos de meia estrela.'),
                'review.max' => __('A resenha pode ter no máximo 2000 caracteres.'),
            ],
        );

        $this->save($request, $game, [
            'rating' => $data['rating'] ?? null,
            'review' => filled($data['review'] ?? null) ? trim($data['review']) : null,
        ]);

        return $this->back($game, __('Avaliação salva.'), 'avaliacoes');
    }

    private function entry(Request $request, Game $game): ?Game
    {
        return $request->user()->library()->whereKey($game->id)->first();
    }

    /** Atualiza a linha da biblioteca; se ficar vazia, remove. */
    private function save(Request $request, Game $game, array $changes): void
    {
        $user = $request->user();
        $current = $this->entry($request, $game)?->pivot;

        $row = [
            'status' => $current?->status,
            'rating' => $current?->rating,
            'hours_logged' => (int) ($current?->hours_logged ?? 0),
            'review' => $current?->review,
            'is_favorite' => (bool) ($current?->is_favorite ?? false),
            ...$changes,
        ];

        $empty = $row['status'] === null && $row['rating'] === null && $row['hours_logged'] === 0
            && blank($row['review']) && ! $row['is_favorite'];

        if ($empty) {
            $user->library()->detach($game->id);

            return;
        }

        $user->library()->syncWithoutDetaching([$game->id => $row]);
    }

    private function back(Game $game, ?string $status = null, ?string $anchor = null): RedirectResponse
    {
        $url = route('games.show', $game).($anchor ? '#'.$anchor : '');

        return $status ? redirect()->to($url)->with('status', $status) : redirect()->to($url);
    }
}
