<?php

namespace App\Enums;

enum PostCategory: string
{
    case Questions = 'questions';
    case Guides = 'guides';
    case Builds = 'builds';
    case Mods = 'mods';
    case Spoilers = 'spoilers';
    case General = 'general';

    public function label(): string
    {
        return match ($this) {
            self::Questions => __('Perguntas'),
            self::Guides => __('Guias'),
            self::Builds => __('Builds'),
            self::Mods => __('Mods'),
            self::Spoilers => __('Spoilers'),
            self::General => __('Geral'),
        };
    }
}
