<?php

namespace Database\Seeders;

use App\Models\NewsItem;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Notícias de exemplo. Os resumos são escritos pela equipe do Lobby e cada uma
     * aponta (campo `url`) para a matéria original — é o link "Leia na fonte".
     * Itens do próprio Lobby apontam para uma página do site (caminho começando com /).
     */
    public function run(): void
    {
        $items = [
            ['eventos', 'Pocket Gamer.biz', '2026-10-01', 'Brasil Game Show abre as portas em São Paulo nesta sexta',
                'A convenção de quatro dias começa em 9 de outubro. O calendário do mês também traz a Paris Games Week, a partir de 22 de outubro, e o Gamescom Asia x Thailand Game Show, em 29 de outubro.', 'https://www.pocketgamer.biz/events/bgs-brasil-game-show-2026/'],
            ['lancamentos', 'DLCompare', '2026-10-05', 'Semana 41 traz Gears of War: E-Day, Star Wars: Galactic Racer e a expansão de Dragon\'s Dogma II',
                'A agenda de 5 a 11 de outubro para PC, PS5 e Xbox também inclui Forever Ago e Silver Pines.', 'https://www.dlcompare.com/gaming-news/this-week-s-video-game-releases-october-2026-week-41'],
            ['assinaturas', 'Pure Xbox', '2026-10-07', 'Game Pass recebe nove jogos entre 8 e 16 de outubro, incluindo Battlefield 6',
                'A lista tem Echo Weaver, Forever Ago e Neon Abyss 2 no dia 8, além de Valor Mortis e Deep Dish Dungeon no dia 13.', 'https://www.purexbox.com/news/2026/10/these-nine-games-are-coming-to-xbox-game-pass-including-battlefield-6-october-8-16'],
            ['plataformas', 'Nintendo Life', '2026-10-05', 'Switch 1 e 2 recebem 15 lançamentos em outubro, incluindo um trio de Resident Evil',
                'Os destaques são Kingdom Hearts Collection em 8 de outubro, Resident Evil 2, 3 e 4 em 16 de outubro e Nintendo Switch Sports Resort em 22 de outubro.', 'https://www.nintendolife.com/news/2026/10/video-15-exciting-new-games-coming-to-switch-1-and-2-in-october-2026'],
            ['selecoes', 'Lobby', '2026-10-03', 'Guia rápido: como começar um Metroidvania sem se perder no mapa',
                'Dicas de exploração, anotações de mapa e quando vale voltar a uma área mais tarde.', '/jogos?genero=Metroidvania'],
            ['selecoes', 'Lobby', '2026-10-02', 'Cinco jogos curtos para terminar em um fim de semana',
                'Aventuras de poucas horas, sem enrolação, para limpar o backlog.', '/jogos'],
            ['selecoes', 'Lobby', '2026-09-29', 'Como organizar sua fila de jogos para zerar sem culpa',
                'Um método simples: poucos jogos ativos, um critério claro para abandonar e uma lista de desejos separada.', '/guias'],
            ['selecoes', 'Lobby', '2026-10-06', 'Os jogos mais jogados da semana entre os membros do Lobby',
                'Hades, Elden Ring e Stardew Valley lideram a lista desta semana.', '/jogos'],
        ];

        foreach ($items as [$category, $source, $date, $title, $summary, $url]) {
            NewsItem::updateOrCreate(
                ['title' => $title],
                [
                    'category' => $category,
                    'source' => $source,
                    'summary' => $summary,
                    'url' => $url,
                    'published_at' => $date.' 12:00:00',
                ],
            );
        }
    }
}
