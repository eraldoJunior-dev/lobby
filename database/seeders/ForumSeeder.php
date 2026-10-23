<?php

namespace Database\Seeders;

use App\Models\ForumThread;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    /**
     * Tópicos de exemplo (uma vez só).
     */
    public function run(): void
    {
        if (ForumThread::exists()) {
            return;
        }

        $users = User::whereIn('email', ['ana@lobby.test', 'lucas@lobby.test', 'bia@lobby.test', 'davi@lobby.test'])->get()->keyBy('email');

        if ($users->count() < 4) {
            return;
        }

        $ana = $users['ana@lobby.test'];
        $lucas = $users['lucas@lobby.test'];
        $bia = $users['bia@lobby.test'];
        $davi = $users['davi@lobby.test'];

        // jogo, autor, título, texto, votos, resolvido, respostas [autor, texto]
        $threads = [
            ['Hollow Knight', $ana, 'Regras e boas-vindas ao fórum de Hollow Knight', 'Respeite quem está começando, use aviso de spoiler e mantenha o assunto no jogo.', 56, false, []],
            ['Hollow Knight', $lucas, 'Ordem recomendada para explorar Hallownest sem se perder', 'Comecei pelas Trilhas Esquecidas e me perdi. Qual ordem vocês recomendam?', 41, true, [
                [$ana, 'Vá primeiro ao Cruzamento Verde e pegue o mapa com o Cornifer antes de descer mais.'],
                [$davi, 'Concordo. Só vá à Cidade das Lágrimas depois de ter o Garra de Louva-a-Deus.'],
            ]],
            ['Stardew Valley', $bia, 'Guia rápido: primeiro ano em Stardew Valley sem estresse', 'Resumo do que priorizar em cada estação para não ficar sem dinheiro.', 38, false, [
                [$lucas, 'Ótimo resumo! Eu acrescentaria: guarde as sementes de morango para a primavera.'],
            ]],
            ['Elden Ring', $davi, 'Regras do fórum de Elden Ring', 'Marque spoilers de história e de chefes. Pedidos de invasão ficam no tópico fixo.', 33, false, []],
            ['Celeste', $ana, 'Alguém já fez todos os lados B?', 'Estou travada no lado B do capítulo 5. Alguma dica de controle?', 12, true, [
                [$bia, 'Treine o dash em sequência nas salas curtas. A prática vale mais que pressa.'],
            ]],
            ['Hades', $lucas, 'Qual a melhor build para começar?', 'Tenho curiosidade sobre qual arma e qual deus combinam melhor no início.', 9, false, []],
            ['Portal 2', $bia, 'Procuro parceiro para o modo cooperativo', 'Alguém quer jogar o coop no fim de semana?', 6, false, []],
            ['The Witcher 3', $davi, 'Vale fazer as missões secundárias antes da principal?', 'Estou no nível 20 e não sei se devo seguir a história.', 4, true, [
                [$ana, 'Faça as secundárias de cada região antes de avançar. Elas rendem equipamento e nível.'],
            ]],
            ['Disco Elysium', $ana, 'Qual habilidade vocês escolheram no começo?', 'Montei um detetive com muito Intelecto. E vocês?', 3, false, []],
            ['Cuphead', $lucas, 'Dicas para o chefe do cassino', 'Esse chefe me derrotou mais de 30 vezes. Como vocês fizeram?', 2, false, []],
        ];

        foreach ($threads as $i => [$gameTitle, $author, $title, $body, $votes, $solved, $replies]) {
            $game = Game::where('title', $gameTitle)->first();

            if (! $game) {
                continue;
            }

            // created_at/updated_at não são preenchíveis em massa; o seeder precisa fixá-los.
            ForumThread::unguarded(function () use ($game, $author, $title, $body, $votes, $solved, $replies, $i) {
                $thread = ForumThread::create([
                    'game_id' => $game->id,
                    'user_id' => $author->id,
                    'title' => $title,
                    'body' => $body,
                    'votes' => $votes,
                    'is_solved' => $solved,
                    'created_at' => now()->subHours(6 + $i * 9),
                    'updated_at' => now()->subHours(6 + $i * 9),
                ]);

                foreach ($replies as $j => [$replyAuthor, $text]) {
                    $thread->replies()->create([
                        'user_id' => $replyAuthor->id,
                        'body' => $text,
                        'created_at' => now()->subHours(4 + $i * 9 - $j),
                        'updated_at' => now()->subHours(4 + $i * 9 - $j),
                    ]);
                }
            });
        }
    }
}
