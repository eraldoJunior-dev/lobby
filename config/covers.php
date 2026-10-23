<?php

/*
|--------------------------------------------------------------------------
| Capas dos jogos
|--------------------------------------------------------------------------
| slug do jogo => [capa principal, alternativas...]
|
| - Jogos da Steam usam a arte vertical oficial (library_600x900) da CDN da Valve.
| - Os que não existem na Steam usam a capa da Wikipedia (Special:FilePath).
| - Se uma capa não carregar, o Lobby mostra o degradê com o título do jogo.
| - Quer garantir tudo offline? Rode `php artisan lobby:covers` (baixa para
|   public/img/covers/) ou coloque você mesmo public/img/covers/{slug}.jpg.
*/

$steam = fn (int $id) => [
    "https://cdn.akamai.steamstatic.com/steam/apps/{$id}/library_600x900.jpg",
    "https://cdn.cloudflare.steamstatic.com/steam/apps/{$id}/library_600x900.jpg",
    "https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/{$id}/library_600x900.jpg",
];

// Aceita mais de um nome de arquivo: se o primeiro não existir, o navegador tenta o próximo.
$wiki = fn (string ...$files) => array_map(
    fn (string $file) => 'https://en.wikipedia.org/wiki/Special:FilePath/'.rawurlencode($file).'?width=400',
    $files,
);

return [
    // Títulos adicionados ao catálogo
    'god-of-war-ragnarok' => $steam(2322010),
    'ghost-of-tsushima' => $steam(2215430),
    'sekiro-shadows-die-twice' => $steam(814380),
    'grand-theft-auto-san-andreas' => $steam(12110),
    'fortnite' => $wiki('Fortnite Battle Royale cover.jpg', 'Fortnite cover.jpg'),
    'valorant' => $wiki('Valorant cover art.jpg', 'Valorant logo.svg'),
    'palworld' => $steam(1623730),
    'resident-evil-village' => $steam(1196590),
    'marvels-spider-man-remastered' => $steam(1817070),
    'marvels-spider-man-miles-morales' => $steam(1817190),
    'marvels-spider-man-2' => $steam(2651280),
    '007-first-light' => $wiki('007 First Light cover art.jpg', '007 First Light.jpg'),
    'hogwarts-legacy' => $steam(990080),
    'the-last-of-us-part-i' => $steam(1888930),
    'the-last-of-us-part-ii-remastered' => $steam(2531310),
    'god-of-war' => $steam(1593500),
    'horizon-zero-dawn' => $steam(1151640),
    'horizon-forbidden-west' => $steam(2420110),
    'death-stranding' => $steam(1190460),
    'days-gone' => $steam(1259420),
    'uncharted-legacy-of-thieves-collection' => $steam(1659420),
    'detroit-become-human' => $steam(1222140),
    'heavy-rain' => $steam(960910),
    'beyond-two-souls' => $steam(960990),
    'resident-evil-2' => $steam(883710),
    'resident-evil-3' => $steam(952060),
    'resident-evil-7-biohazard' => $steam(418370),
    'resident-evil-4-2005' => $steam(254700),
    'silent-hill-2' => $steam(2124490),
    'alan-wake-2' => $wiki('Alan Wake 2 cover art.jpg', 'Alan Wake II cover.jpg'),
    'dead-space' => $steam(1693980),
    'outlast' => $steam(238320),
    'outlast-2' => $steam(414700),
    'dying-light' => $steam(239140),
    'dying-light-2-stay-human' => $steam(534380),
    'left-4-dead-2' => $steam(550),
    'counter-strike-2' => $steam(730),
    'apex-legends' => $steam(1172470),
    'pubg-battlegrounds' => $steam(578080),
    'call-of-duty-black-ops-iii' => $steam(311210),
    'battlefield-1' => $steam(1238840),
    'battlefield-v' => $steam(1238810),
    'ea-sports-fc-25' => $steam(2669320),
    'nba-2k25' => $steam(2878980),
    'f1-24' => $steam(2488620),
    'need-for-speed-heat' => $steam(1222680),
    'need-for-speed-unbound' => $steam(1846380),
    'the-crew-motorfest' => $steam(2698940),
    'assassins-creed-valhalla' => $steam(2208920),
    'assassins-creed-odyssey' => $steam(812140),
    'assassins-creed-origins' => $steam(582160),
    'far-cry-5' => $steam(552520),
    'far-cry-6' => $steam(2369390),

    // --- Steam ---
    'cuphead' => $steam(268910),
    'hollow-knight' => $steam(367520),
    'hades' => $steam(1145360),
    'portal-2' => $steam(620),
    'stardew-valley' => $steam(413150),
    'elden-ring' => $steam(1245620),
    'celeste' => $steam(504230),
    'disco-elysium' => $steam(632470),
    'grand-theft-auto-v' => $steam(271590),
    'the-elder-scrolls-v-skyrim' => $steam(489830),
    'the-witcher-3' => $steam(292030),
    'red-dead-redemption-2' => $steam(1174180),
    'cyberpunk-2077' => $steam(1091500),
    'baldur-s-gate-3' => $steam(1086940),
    'baldurs-gate-3' => $steam(1086940),
    'undertale' => $steam(391540),
    'final-fantasy-vii' => $steam(39140),
    'doom-eternal' => $steam(782330),
    'forza-horizon-5' => $steam(1551360),
    'resident-evil-4' => $steam(2050650),
    'god-of-war' => $steam(1593500),

    // --- Fora da Steam (Wikipedia) ---
    'super-mario-odyssey' => $wiki('Super Mario Odyssey.jpg'),
    'super-metroid' => $wiki('Smetroidbox.jpg', 'Super Metroid box art.jpg'),
    'bloodborne' => $wiki('Bloodborne Cover Wallpaper.jpg'),
    'the-legend-of-zelda-breath-of-the-wild' => $wiki('The Legend of Zelda Breath of the Wild.jpg'),
    'the-legend-of-zelda-tears-of-the-kingdom' => $wiki('The Legend of Zelda Tears of the Kingdom cover.jpg'),
    'minecraft' => $wiki('Minecraft 2024 cover art.png', 'Cover Art of Minecraft.jpg', 'Minecraft cover.png', 'Minecraft Java Edition cover.png'),
];
