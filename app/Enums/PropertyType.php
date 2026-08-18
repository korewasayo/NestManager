<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PropertyType: string implements HasLabel
{
    case APARTMENT = 'apartment';
    case HOUSE = 'house';
    case VILLA = 'villa';
    case ROOM = 'room';
    case STUDIO = 'studio';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::APARTMENT => ucfirst('apartment'),
            self::HOUSE => ucfirst('house'),
            self::VILLA => ucfirst('villa'),
            self::ROOM => ucfirst('room'),
            self::STUDIO => ucfirst('studio'),
        };
    }
}