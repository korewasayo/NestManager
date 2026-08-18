<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AmenityCategory: string implements HasLabel
{
    case GENERAL = 'general';
    case KITCHEN = 'kitchen';
    case BATHROOM = 'bathroom';
    case OUTDOOR = 'outdoor';
    case SAFETY = 'safety';
    case ENTERTAINMENT = 'entertainment';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::GENERAL => ucfirst('general'),
            self::KITCHEN => ucfirst('kitchen'),
            self::BATHROOM => ucfirst('bathroom'),
            self::OUTDOOR => ucfirst('outdoor'),
            self::SAFETY => ucfirst('safety'),
            self::ENTERTAINMENT => ucfirst('entertainment'),
        };
    }
}