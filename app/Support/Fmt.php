<?php

namespace App\Support;

use Carbon\CarbonInterface;

/** Formatação de números e datas conforme o idioma ativo. */
class Fmt
{
    public static function num(int|float|string|null $value, int $decimals = 0): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return app()->getLocale() === 'en'
            ? number_format((float) $value, $decimals, '.', ',')
            : number_format((float) $value, $decimals, ',', '.');
    }

    /** "5 de out" (pt-BR) / "Oct 5" (en). */
    public static function shortDate(CarbonInterface $date): string
    {
        return $date->locale(app()->getLocale())->translatedFormat(app()->getLocale() === 'en' ? 'M j' : 'j \d\e M');
    }

    /** "5 de outubro de 2026" (pt-BR) / "October 5, 2026" (en). */
    public static function longDate(CarbonInterface $date): string
    {
        return $date->locale(app()->getLocale())->translatedFormat(app()->getLocale() === 'en' ? 'F j, Y' : 'j \d\e F \d\e Y');
    }

    /** "22/10/2026 00:55" (pt-BR) / "10/22/2026 12:55 AM" (en). */
    public static function dateTime(CarbonInterface $date): string
    {
        return app()->getLocale() === 'en' ? $date->format('m/d/Y h:i A') : $date->format('d/m/Y H:i');
    }
}
