<?php
namespace App\Support;
final class Platforms {
    public const OPTIONS = ['pc' => ['PC', '🖥️'], 'xbox' => ['Xbox', '🎮'], 'nintendo' => ['Nintendo', '🔴'], 'psn' => ['PSN', '🔷'], 'mobile' => ['Mobile', '📱']];
    public static function selected($user): array { return array_values(array_intersect(array_keys(self::OPTIONS), (array) $user->setting('gaming_platforms', []))); }
}
