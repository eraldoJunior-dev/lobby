<?php

namespace App\Support;

use App\Models\Game;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/** Steam Store metadata, sourced by Steam App ID; never substitute fabricated values. */
final class GameFacts
{
    public static function steamId(Game $game): ?int
    {
        $covers = config('covers.'.$game->slug, []);
        foreach ((array) $covers as $url) {
            if (preg_match('~/steam/apps/(\d+)/~', (string) $url, $matches)) {
                return (int) $matches[1];
            }
        }
        return null;
    }

    public static function get(Game $game, bool $refresh = false): ?array
    {
        $id = self::steamId($game);
        // RAWG cobre títulos fora da Steam (inclusive exclusivos de consoles).
        if (! $id) return self::rawg($game, $refresh);
        $key = 'lobby:steam:metadata:'.$id;
        if ($refresh) Cache::forget($key);
        $steam = Cache::remember($key, now()->addDays(7), function () use ($id) {
            try {
                $response = Http::timeout(8)->retry(1, 400)->get('https://store.steampowered.com/api/appdetails', [
                    'appids' => $id, 'cc' => 'br', 'l' => 'brazilian',
                ]);
                if (! $response->successful()) return null;
                $entry = $response->json((string) $id);
                if (! is_array($entry) || ! ($entry['success'] ?? false)) return null;
                $data = $entry['data'] ?? [];
                if (($data['type'] ?? '') !== 'game') return null;
                $categories = $data['categories'] ?? [];
                $achievementCount = $data['achievements']['total'] ?? null;
                return [
                    'developer' => implode(', ', $data['developers'] ?? []),
                    'publisher' => implode(', ', $data['publishers'] ?? []),
                    'release' => $data['release_date']['date'] ?? null,
                    'description' => strip_tags($data['short_description'] ?? ''),
                    'platforms' => implode(', ', array_keys(array_filter($data['platforms'] ?? []))),
                    'price' => $data['is_free'] ?? false ? 'Grátis' : ($data['price_overview']['final_formatted'] ?? null),
                    'achievements' => is_numeric($achievementCount) ? (int) $achievementCount : null,
                    'store_url' => 'https://store.steampowered.com/app/'.$id.'/',
                    'achievements_url' => 'https://steamcommunity.com/stats/'.$id.'/achievements/',
                    'source' => 'Steam Store',
                    'stores' => [['name' => 'Steam', 'url' => 'https://store.steampowered.com/app/'.$id.'/', 'price' => ($data['is_free'] ?? false) ? 'Grátis' : ($data['price_overview']['final_formatted'] ?? null)]],
                ];
            } catch (\Throwable $e) {
                report($e);
                return null;
            }
        });
        // Se a Steam não devolver metadados, buscar outra fonte verificável.
        return $steam ?: self::rawg($game, $refresh);
    }

    public static function rawg(Game $game, bool $refresh = false): ?array
    {
        $apiKey = config('services.rawg.key');
        if (! $apiKey) return null;
        $cacheKey = 'lobby:rawg:metadata:'.$game->id.':'.md5($game->title);
        if ($refresh) Cache::forget($cacheKey);
        return Cache::remember($cacheKey, now()->addDays(7), function () use ($game, $apiKey) {
            try {
                $search = Http::timeout(10)->get('https://api.rawg.io/api/games', [
                    'key' => $apiKey, 'search' => $game->title,
                    'search_precise' => 'true', 'page_size' => 10,
                ]);
                if (! $search->successful()) return null;
                $normalize = static fn ($title) => preg_replace('/[^a-z0-9]+/', '', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', (string) $title) ?: (string) $title));
                $match = collect($search->json('results', []))->first(
                    fn ($result) => $normalize($result['name'] ?? '') === $normalize($game->title)
                );
                // Não atribuir dados de jogos diferentes só porque o título é parecido.
                if (! $match || empty($match['id'])) return null;
                $response = Http::timeout(10)->get('https://api.rawg.io/api/games/'.$match['id'], ['key' => $apiKey]);
                if (! $response->successful()) return null;
                $data = $response->json();
                $sourceUrl = 'https://rawg.io/games/'.($data['slug'] ?? $match['slug']);
                return [
                    'developer' => implode(', ', array_column($data['developers'] ?? [], 'name')),
                    'publisher' => implode(', ', array_column($data['publishers'] ?? [], 'name')),
                    'release' => $data['released'] ?? null,
                    'description' => trim(strip_tags($data['description'] ?? $data['description_raw'] ?? '')),
                    'platforms' => implode(', ', array_filter(array_map(static fn ($v) => $v['platform']['name'] ?? null, $data['platforms'] ?? []))),
                    'price' => null,
                    'achievements' => isset($data['achievements_count']) && is_numeric($data['achievements_count']) ? (int) $data['achievements_count'] : null,
                    'store_url' => $sourceUrl,
                    'achievements_url' => $sourceUrl,
                    'source' => 'RAWG',
                    'stores' => self::rawgStores($data),
                ];
            } catch (\Throwable $e) {
                report($e);
                return null;
            }
        });
    }
    /** Loja identificada pelo catálogo RAWG; links não representam preço verificado. */
    private static function rawgStores(array $data): array
    {
        $stores = [];
        foreach (($data['stores'] ?? []) as $entry) {
            $name = trim((string) ($entry['store']['name'] ?? ''));
            $domain = strtolower((string) ($entry['store']['domain'] ?? ''));
            $url = $entry['url'] ?? null;
            // RAWG às vezes só fornece a loja, não a página do produto.
            if (! is_string($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
                $url = $domain ? 'https://'.$domain : null;
            }
            if ($name && $url && parse_url($url, PHP_URL_SCHEME) === 'https') {
                $stores[] = ['name' => $name, 'url' => $url, 'price' => null];
            }
        }
        return $stores;
    }

    /** Tempos editoriais só podem ser exibidos após inclusão com fonte documentada. */
    public static function completionTimes(Game $game): ?array
    {
        $file = base_path('data/verified_completion_times.json');
        if (! is_file($file)) return null;
        $entries = json_decode((string) file_get_contents($file), true);
        $entry = is_array($entries) ? ($entries[$game->slug] ?? null) : null;
        if (! is_array($entry) || empty($entry['source_url']) ||
            ! filter_var($entry['source_url'], FILTER_VALIDATE_URL)) return null;
        $main = $entry['main_hours'] ?? null;
        $full = $entry['completionist_hours'] ?? null;
        if ((! is_numeric($main) || $main <= 0) && (! is_numeric($full) || $full <= 0)) return null;
        return [
            'main_hours' => is_numeric($main) && $main > 0 ? (float) $main : null,
            'completionist_hours' => is_numeric($full) && $full > 0 ? (float) $full : null,
            'source_url' => $entry['source_url'],
        ];
    }

}
