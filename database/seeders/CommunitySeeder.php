<?php

namespace Database\Seeders;

use App\Enums\ReactionType;
use App\Models\Game;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    /**
     * Usuários e publicações de exemplo (senha de todos: "password").
     */
    public function run(): void
    {
        $users = collect([
            ['Ana Beatriz', 'ana@lobby.test'],
            ['Lucas Prado', 'lucas@lobby.test'],
            ['Bia Moura', 'bia@lobby.test'],
            ['Davi Nunes', 'davi@lobby.test'],
        ])->map(fn ($u) => User::firstOrCreate(['email' => $u[1]], ['name' => $u[0], 'password' => 'password']));

        [$ana, $lucas, $bia, $davi] = $users->all();

        // Só popula as publicações uma vez.
        if (Post::exists()) {
            return;
        }

        $game = fn (string $title) => Game::where('title', $title)->value('id');

        $posts = [
            [$ana, 'Hades', 5, 'Cada run me deixa com vontade de mais uma.', false, 2],
            [$lucas, 'Portal 2', 5, 'Os puzzles do coop são geniais. Joguem com alguém que vocês gostam (e que aguenta rir de vocês).', false, 4],
            [$bia, 'Stardew Valley', 5, 'Terminei o primeiro ano e já quero o segundo.', false, 6],
            [$davi, 'Elden Ring', 4, 'Cheguei no chefe da Capital e passei duas horas só aprendendo o padrão de ataque dele. Valeu cada tentativa.', true, 9],
            [$ana, 'Celeste', 5, 'A trilha sonora sozinha já valia a compra.', false, 14],
            [$lucas, 'Hollow Knight', 5, 'Dica pra quem está começando: converse com todo mundo e explore antes de seguir a história.', false, 20],
            [$bia, 'Cuphead', 4, 'Difícil, bonito e injusto na medida certa.', false, 27],
            [$davi, null, null, 'Alguém tem recomendação de jogo curto pra terminar no fim de semana?', false, 31],
        ];

        $created = collect($posts)->map(function ($p) use ($game) {
            [$author, $title, $rating, $body, $spoiler, $hoursAgo] = $p;

            return Post::factory()->for($author)->create([
                'game_id' => $title ? $game($title) : null,
                'rating' => $rating,
                'body' => $body,
                'contains_spoilers' => $spoiler,
                'created_at' => now()->subHours($hoursAgo),
                'updated_at' => now()->subHours($hoursAgo),
            ]);
        });

        // Reações determinísticas, só para o feed não nascer vazio.
        $types = ReactionType::cases();

        foreach ($created as $post) {
            foreach ($users as $i => $user) {
                foreach ($types as $j => $type) {
                    if (($post->id + $i + $j) % 3 === 0) {
                        $post->reactions()->firstOrCreate(['user_id' => $user->id, 'type' => $type->value]);
                    }
                }
            }
        }

        // Quem o usuário de teste segue (para a aba "Seguindo" ter conteúdo).
        $test = User::where('email', 'test@example.com')->first();
        $test?->following()->syncWithoutDetaching([$ana->id, $lucas->id]);
    }
}
