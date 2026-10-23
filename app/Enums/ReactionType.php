<?php

namespace App\Enums;

enum ReactionType: string
{
    case Like = 'like';
    case Love = 'love';
    case Fire = 'fire';
    case Wow = 'wow';
    case Laugh = 'laugh';
    case Sad = 'sad';

    public function label(): string
    {
        return match ($this) {
            self::Like => __('Curtir'),
            self::Love => __('Amei'),
            self::Fire => __('Pegando fogo'),
            self::Wow => __('Uau'),
            self::Laugh => __('Engraçado'),
            self::Sad => __('Triste'),
        };
    }

    /** Nome do ícone no sprite (resources/views/partials/icons.blade.php). */
    public function icon(): string
    {
        return match ($this) {
            self::Like => 'thumbs-up',
            self::Love => 'heart',
            self::Fire => 'flame',
            self::Wow => 'wow',
            self::Laugh => 'laugh',
            self::Sad => 'frown',
        };
    }
}
