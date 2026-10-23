<?php

namespace App\Support;

use App\Models\User;

/** Preferências de aparência (guardadas em users.settings.appearance). */
class Appearance
{
    /** roxo = padrão do Lobby, dark = preto, light = branco */
    public const THEMES = ['purple', 'dark', 'light'];

    public const ACCENTS = ['purple', 'teal', 'orange', 'pink', 'blue', 'green'];

    public const DENSITIES = ['comfortable', 'compact'];

    public const TEXT_SIZES = ['normal', 'large'];

    /** @var array<string, string|bool> */
    public const DEFAULTS = [
        'theme' => 'purple',
        'accent' => 'purple',
        'density' => 'comfortable',
        'text_size' => 'normal',
        'reduce_motion' => false,
        'high_contrast' => false,
        'hide_spoilers' => true,
    ];

    /** @return array<string, string|bool> */
    public static function for(?User $user): array
    {
        $saved = $user ? (array) $user->setting('appearance', []) : [];

        return self::sanitize(array_merge(self::DEFAULTS, $saved));
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, string|bool>
     */
    public static function sanitize(array $input): array
    {
        $pick = fn (string $key, array $allowed) => in_array($input[$key] ?? null, $allowed, true)
            ? $input[$key]
            : self::DEFAULTS[$key];

        return [
            'theme' => $pick('theme', self::THEMES),
            'accent' => $pick('accent', self::ACCENTS),
            'density' => $pick('density', self::DENSITIES),
            'text_size' => $pick('text_size', self::TEXT_SIZES),
            'reduce_motion' => filter_var($input['reduce_motion'] ?? self::DEFAULTS['reduce_motion'], FILTER_VALIDATE_BOOLEAN),
            'high_contrast' => filter_var($input['high_contrast'] ?? self::DEFAULTS['high_contrast'], FILTER_VALIDATE_BOOLEAN),
            'hide_spoilers' => filter_var($input['hide_spoilers'] ?? self::DEFAULTS['hide_spoilers'], FILTER_VALIDATE_BOOLEAN),
        ];
    }
}
