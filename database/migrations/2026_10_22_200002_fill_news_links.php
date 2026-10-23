<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Preenche o link "Leia na fonte" das notícias que já estavam no banco sem URL. */
    public function up(): void
    {
        $links = [
            'Brasil Game Show abre as portas em São Paulo nesta sexta' => ['https://www.pocketgamer.biz/events/bgs-brasil-game-show-2026/', 'Pocket Gamer.biz'],
            "Semana 41 traz Gears of War: E-Day, Star Wars: Galactic Racer e a expansão de Dragon's Dogma II" => ['https://www.dlcompare.com/gaming-news/this-week-s-video-game-releases-october-2026-week-41', null],
            'Game Pass recebe nove jogos entre 8 e 16 de outubro, incluindo Battlefield 6' => ['https://www.purexbox.com/news/2026/10/these-nine-games-are-coming-to-xbox-game-pass-including-battlefield-6-october-8-16', null],
            'Switch 1 e 2 recebem 15 lançamentos em outubro, incluindo um trio de Resident Evil' => ['https://www.nintendolife.com/news/2026/10/video-15-exciting-new-games-coming-to-switch-1-and-2-in-october-2026', null],
            'Guia rápido: como começar um Metroidvania sem se perder no mapa' => ['/jogos?genero=Metroidvania', null],
            'Cinco jogos curtos para terminar em um fim de semana' => ['/jogos', null],
            'Como organizar sua fila de jogos para zerar sem culpa' => ['/guias', null],
            'Os jogos mais jogados da semana entre os membros do Lobby' => ['/jogos', null],
        ];

        foreach ($links as $title => [$url, $source]) {
            $changes = ['url' => $url];
            if ($source) {
                $changes['source'] = $source;
            }

            DB::table('news_items')->where('title', $title)->whereNull('url')->update($changes);
        }
    }

    public function down(): void
    {
        // Sem volta: os links são só dados.
    }
};
