<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GameSeeder extends Seeder
{
    /**
     * Catálogo de exemplo. Notas, horas e membros são valores ilustrativos para o protótipo.
     */
    public function run(): void
    {
        // título, ano, gênero, nota, horas, cor 1, cor 2, membros
        $games = [
            ['Super Mario Odyssey', 2017, 'Plataforma', 4.7, 13, '#e0454a', '#2c3f7a', 2810],
            ['Super Metroid', 1994, 'Metroidvania', 4.5, 7, '#5b3fa0', '#1c5f75', 1320],
            ['Cuphead', 2017, 'Plataforma', 4.4, 5, '#f4c20d', '#d98a0b', 1980],
            ['Bloodborne', 2015, 'Ação e RPG', 4.6, 32, '#7a1f2b', '#2a0f18', 2240],
            ['Hollow Knight', 2017, 'Metroidvania', 4.7, 27, '#2b3a6b', '#0e1224', 3070],
            ['Hades', 2020, 'Roguelike', 4.8, 22, '#c2362f', '#3b0f1f', 2942],
            ['Portal 2', 2011, 'Puzzle', 4.8, 9, '#2a8fbd', '#14263a', 2367],
            ['Stardew Valley', 2016, 'Simulação', 4.6, 53, '#5aa650', '#1f4a3a', 3663],
            ['Elden Ring', 2022, 'Ação e RPG', 4.7, 60, '#b8892b', '#2b2210', 4017],
            ['Celeste', 2018, 'Plataforma', 4.6, 8, '#d1578f', '#3a2a6b', 2552],
            ['Disco Elysium', 2019, 'RPG', 4.6, 24, '#c4783a', '#2f2a3d', 1645],
            ['The Legend of Zelda: Breath of the Wild', 2017, 'Aventura', 4.8, 50, '#4aa3a0', '#1c3b4a', 3890],
            ['Grand Theft Auto V', 2013, 'Ação e Aventura', 4.3, 32, '#e8a33d', '#2a2f4a', 5120],
            ['The Elder Scrolls V: Skyrim', 2011, 'Ação e RPG', 4.5, 33, '#8a98a8', '#1b222b', 4380],
            ['The Witcher 3', 2015, 'Ação e RPG', 4.5, 52, '#9aa3ad', '#2b2f36', 4400],
            ['Minecraft', 2011, 'Sandbox', 4.6, 15, '#7a8a2b', '#3a3a12', 6010],
            ['Red Dead Redemption 2', 2018, 'Ação e Aventura', 4.6, 50, '#d2382b', '#3a0e0b', 3320],
            ['Cyberpunk 2077', 2020, 'Ação e RPG', 4.2, 26, '#f2e600', '#2a2a2a', 2790],
            ['Baldur\'s Gate 3', 2023, 'RPG', 4.8, 75, '#3a6ea5', '#1a1530', 3540],
            ['Undertale', 2015, 'RPG', 4.7, 7, '#1a1a1a', '#000000', 1890],
            ['Final Fantasy VII', 1997, 'JRPG', 4.5, 40, '#3a8f9a', '#14253a', 1730],
            ['Doom Eternal', 2020, 'Tiro', 4.5, 13, '#d4501f', '#2a0e08', 1560],
            ['Forza Horizon 5', 2021, 'Corrida', 4.5, 20, '#f08a2b', '#1f4a6b', 1410],
            ['Resident Evil 4', 2023, 'Terror', 4.6, 16, '#6b1f1f', '#14100f', 1980],
        ];

        // Novos títulos. updateOrCreate evita duplicações ao executar o seeder novamente.
        $games = array_merge($games, [
            ['God of War Ragnarök', 2022, 'Ação e Aventura', 4.5, 26, '#5b3fa0', '#1c1933', 0],
            ['Ghost of Tsushima', 2020, 'Ação e Aventura', 4.5, 25, '#5b3fa0', '#1c1933', 0],
            ['Sekiro: Shadows Die Twice', 2019, 'Ação e Aventura', 4.5, 30, '#5b3fa0', '#1c1933', 0],
            ['Grand Theft Auto: San Andreas', 2004, 'Ação e Aventura', 4.5, 31, '#5b3fa0', '#1c1933', 0],
            ['Fortnite', 2017, 'Battle Royale', 4.5, 0, '#5b3fa0', '#1c1933', 0],
            ['Valorant', 2020, 'Tiro', 4.5, 0, '#5b3fa0', '#1c1933', 0],
            ['Palworld', 2024, 'Sobrevivência', 4.5, 35, '#5b3fa0', '#1c1933', 0],
            ['Resident Evil Village', 2021, 'Terror', 4.5, 10, '#5b3fa0', '#1c1933', 0],
            ['Marvel’s Spider-Man Remastered', 2022, 'Ação e Aventura', 4.5, 17, '#5b3fa0', '#1c1933', 0],
            ['Marvel’s Spider-Man: Miles Morales', 2022, 'Ação e Aventura', 4.5, 8, '#5b3fa0', '#1c1933', 0],
            ['Marvel’s Spider-Man 2', 2025, 'Ação e Aventura', 4.5, 18, '#5b3fa0', '#1c1933', 0],
            ['007 First Light', 2026, 'Ação e Aventura', 4.5, 0, '#5b3fa0', '#1c1933', 0],
            ['Hogwarts Legacy', 2023, 'Ação e RPG', 4.5, 27, '#5b3fa0', '#1c1933', 0],
            ['The Last of Us Part I', 2023, 'Ação e Aventura', 4.5, 15, '#5b3fa0', '#1c1933', 0],
            ['The Last of Us Part II Remastered', 2025, 'Ação e Aventura', 4.5, 24, '#5b3fa0', '#1c1933', 0],
            ['God of War', 2018, 'Ação e Aventura', 4.5, 21, '#5b3fa0', '#1c1933', 0],
            ['Horizon Zero Dawn', 2017, 'Ação e RPG', 4.5, 23, '#5b3fa0', '#1c1933', 0],
            ['Horizon Forbidden West', 2022, 'Ação e RPG', 4.5, 28, '#5b3fa0', '#1c1933', 0],
            ['Death Stranding', 2019, 'Ação e Aventura', 4.5, 40, '#5b3fa0', '#1c1933', 0],
            ['Days Gone', 2019, 'Ação e Aventura', 4.5, 36, '#5b3fa0', '#1c1933', 0],
            ['Uncharted: Legacy of Thieves Collection', 2022, 'Ação e Aventura', 4.5, 18, '#5b3fa0', '#1c1933', 0],
            ['Detroit: Become Human', 2018, 'Aventura', 4.5, 12, '#5b3fa0', '#1c1933', 0],
            ['Heavy Rain', 2010, 'Aventura', 4.5, 10, '#5b3fa0', '#1c1933', 0],
            ['Beyond: Two Souls', 2013, 'Aventura', 4.5, 10, '#5b3fa0', '#1c1933', 0],
            ['Resident Evil 2', 2019, 'Terror', 4.5, 9, '#5b3fa0', '#1c1933', 0],
            ['Resident Evil 3', 2020, 'Terror', 4.5, 6, '#5b3fa0', '#1c1933', 0],
            ['Resident Evil 7 Biohazard', 2017, 'Terror', 4.5, 9, '#5b3fa0', '#1c1933', 0],
            ['Resident Evil 4 (2005)', 2005, 'Terror', 4.5, 16, '#5b3fa0', '#1c1933', 0],
            ['Silent Hill 2', 2024, 'Terror', 4.5, 16, '#5b3fa0', '#1c1933', 0],
            ['Alan Wake 2', 2023, 'Terror', 4.5, 18, '#5b3fa0', '#1c1933', 0],
            ['Dead Space', 2023, 'Terror', 4.5, 12, '#5b3fa0', '#1c1933', 0],
            ['Outlast', 2013, 'Terror', 4.5, 5, '#5b3fa0', '#1c1933', 0],
            ['Outlast 2', 2017, 'Terror', 4.5, 7, '#5b3fa0', '#1c1933', 0],
            ['Dying Light', 2015, 'Sobrevivência', 4.5, 18, '#5b3fa0', '#1c1933', 0],
            ['Dying Light 2 Stay Human', 2022, 'Sobrevivência', 4.5, 22, '#5b3fa0', '#1c1933', 0],
            ['Left 4 Dead 2', 2009, 'Tiro', 4.5, 10, '#5b3fa0', '#1c1933', 0],
            ['Counter-Strike 2', 2023, 'Tiro', 4.5, 0, '#5b3fa0', '#1c1933', 0],
            ['Apex Legends', 2019, 'Battle Royale', 4.5, 0, '#5b3fa0', '#1c1933', 0],
            ['PUBG: Battlegrounds', 2017, 'Battle Royale', 4.5, 0, '#5b3fa0', '#1c1933', 0],
            ['Call of Duty: Black Ops III', 2015, 'Tiro', 4.5, 9, '#5b3fa0', '#1c1933', 0],
            ['Battlefield 1', 2016, 'Tiro', 4.5, 7, '#5b3fa0', '#1c1933', 0],
            ['Battlefield V', 2018, 'Tiro', 4.5, 6, '#5b3fa0', '#1c1933', 0],
            ['EA Sports FC 25', 2024, 'Esporte', 4.5, 0, '#5b3fa0', '#1c1933', 0],
            ['NBA 2K25', 2024, 'Esporte', 4.5, 0, '#5b3fa0', '#1c1933', 0],
            ['F1 24', 2024, 'Corrida', 4.5, 0, '#5b3fa0', '#1c1933', 0],
            ['Need for Speed Heat', 2019, 'Corrida', 4.5, 13, '#5b3fa0', '#1c1933', 0],
            ['Need for Speed Unbound', 2022, 'Corrida', 4.5, 16, '#5b3fa0', '#1c1933', 0],
            ['The Crew Motorfest', 2023, 'Corrida', 4.5, 20, '#5b3fa0', '#1c1933', 0],
            ['Assassin’s Creed Valhalla', 2020, 'Ação e RPG', 4.5, 60, '#5b3fa0', '#1c1933', 0],
            ['Assassin’s Creed Odyssey', 2018, 'Ação e RPG', 4.5, 45, '#5b3fa0', '#1c1933', 0],
            ['Assassin’s Creed Origins', 2017, 'Ação e RPG', 4.5, 30, '#5b3fa0', '#1c1933', 0],
            ['Far Cry 5', 2018, 'Tiro', 4.5, 18, '#5b3fa0', '#1c1933', 0],
            ['Far Cry 6', 2021, 'Tiro', 4.5, 23, '#5b3fa0', '#1c1933', 0],
        ]);

        foreach ($games as [$title, $year, $genre, $rating, $hours, $from, $to, $members]) {
            Game::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'year' => $year,
                    'genre' => $genre,
                    'rating' => $rating,
                    'hours_to_beat' => $hours,
                    'cover_from' => $from,
                    'cover_to' => $to,
                    'members_count' => $members,
                ],
            );
        }
    }
}
