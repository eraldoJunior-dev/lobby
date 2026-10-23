<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Guide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GuideSeeder extends Seeder
{
    /**
     * Guias de exemplo com checklist (conteúdo ilustrativo para o protótipo).
     */
    public function run(): void
    {
        // jogo, tipo, título, dificuldade, horas, itens
        $guides = [
            ['Hollow Knight', '100', 'Hollow Knight: roteiro de 112% de conclusão', 4, '60-80 h', [
                'Derrotar os cinco Sonhadores', 'Coletar todos os fragmentos de máscara', 'Coletar todos os fragmentos de vaso',
                'Obter todos os encantamentos de amuleto', 'Completar o Coliseu dos Tolos', 'Concluir o Panteão de Hallownest',
                'Encontrar todos os mapas e o Cartógrafo', 'Libertar todos os Grubs',
            ]],
            ['Elden Ring', 'platina', 'Elden Ring: roteiro do troféu de platina', 4, '120-150 h', [
                'Derrotar todos os chefes principais', 'Concluir as missões de cada NPC importante', 'Ver os finais disponíveis',
                'Coletar todas as cinzas espirituais', 'Obter todos os talismãs', 'Encontrar todos os feitiços e encantamentos',
            ]],
            ['Celeste', '100', 'Celeste: todos os 32 morangos e lados B', 3, '25-40 h', [
                'Coletar os morangos do Capítulo 1 ao 7', 'Encontrar todos os corações de cristal', 'Completar os lados B',
                'Completar os lados C', 'Terminar o Capítulo 9', 'Coletar todas as fitas de gravação',
            ]],
            ['Stardew Valley', 'conquistas', 'Stardew Valley: perfeição e lista de conquistas', 3, '150+ h', [
                'Completar o centro comunitário', 'Atingir o nível 10 em todas as habilidades', 'Obter todas as receitas de cozinha',
                'Construir todos os edifícios da fazenda', 'Terminar a mina até o fundo', 'Fazer amizade máxima com todos os moradores',
            ]],
            ['Hades', 'conquistas', 'Hades: todas as 49 conquistas', 3, '60-100 h', [
                'Escapar do Tártaro pela primeira vez', 'Fugir 10 vezes', 'Aumentar o laço com todos os deuses',
                'Usar todas as armas', 'Alcançar o calor máximo', 'Concluir o Espelho da Noite',
            ]],
            ['Portal 2', 'conquistas', 'Portal 2: todas as 51 conquistas, incluindo o coop', 2, '15-20 h', [
                'Terminar a campanha para um jogador', 'Terminar a campanha cooperativa', 'Resolver as câmaras sem usar tiros desnecessários',
                'Encontrar os segredos escondidos', 'Completar os desafios de velocidade',
            ]],
        ];

        foreach ($guides as [$gameTitle, $type, $title, $difficulty, $hours, $items]) {
            $game = Game::where('title', $gameTitle)->first();

            if (! $game) {
                continue;
            }

            $guide = Guide::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'game_id' => $game->id,
                    'title' => $title,
                    'type' => $type,
                    'difficulty' => $difficulty,
                    'hours_label' => $hours,
                ],
            );

            if ($guide->items()->exists()) {
                continue;
            }

            foreach ($items as $i => $item) {
                $guide->items()->create(['title' => $item, 'position' => $i + 1]);
            }
        }
    }
}
