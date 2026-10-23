<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    /**
     * Biblioteca de exemplo para o perfil não nascer vazio.
     */
    public function run(): void
    {
        // e-mail => [jogo, status, nota, horas, resenha, favorito]
        $libraries = [
            'test@example.com' => [
                ['Hollow Knight', 'zerado', 4.5, 27, 'Mundo enorme e cheio de segredos.', true],
                ['Celeste', 'zerado', 4.4, 8, 'Difícil e emocionante.', true],
                ['The Witcher 3', 'jogando', 4.5, 52, 'Ótimas missões secundárias.', true],
                ['Stardew Valley', 'jogando', null, 53, null, true],
                ['Elden Ring', 'backlog', null, 0, null, false],
                ['Hades', 'backlog', null, 0, null, false],
            ],
            'ana@lobby.test' => [
                ['Hades', 'zerado', 5, 40, 'Cada run me deixa com vontade de mais uma.', true],
                ['Celeste', 'zerado', 5, 12, null, true],
                ['Disco Elysium', 'jogando', 4.5, 10, null, false],
            ],
            'lucas@lobby.test' => [
                ['Portal 2', 'zerado', 5, 12, null, true],
                ['Hollow Knight', 'zerado', 5, 45, null, true],
                ['Cuphead', 'jogando', 4, 6, null, false],
            ],
            'bia@lobby.test' => [
                ['Stardew Valley', 'zerado', 5, 120, null, true],
                ['Cuphead', 'zerado', 4, 9, null, false],
            ],
            'davi@lobby.test' => [
                ['Elden Ring', 'jogando', 4, 70, null, true],
                ['The Witcher 3', 'backlog', null, 0, null, false],
            ],
        ];

        foreach ($libraries as $email => $rows) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                continue;
            }

            foreach ($rows as [$title, $status, $rating, $hours, $review, $favorite]) {
                $game = Game::where('title', $title)->first();

                if (! $game) {
                    continue;
                }

                $user->library()->syncWithoutDetaching([
                    $game->id => [
                        'status' => $status,
                        'rating' => $rating,
                        'hours_logged' => $hours,
                        'review' => $review,
                        'is_favorite' => $favorite,
                    ],
                ]);
            }
        }
    }
}
