<?php

namespace App\Support;

/** Idiomas disponíveis no Lobby. A chave é o código usado pelo Laravel. */
class Locales
{
    /** @var array<string, string> */
    public const SUPPORTED = [
        'pt_BR' => 'Português (Brasil)',
        'en' => 'English (US)',
    ];

    public const DEFAULT = 'pt_BR';

    public static function isSupported(?string $locale): bool
    {
        return is_string($locale) && array_key_exists($locale, self::SUPPORTED);
    }

    /** Código curto usado no atributo lang do HTML. */
    public static function htmlLang(?string $locale = null): string
    {
        return ($locale ?? app()->getLocale()) === 'en' ? 'en-US' : 'pt-BR';
    }
}
