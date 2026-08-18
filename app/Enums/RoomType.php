<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RoomType: string implements HasLabel
{
    case SINGLE = 'single';
    case DOUBLE = 'double';
    case TWIN = 'twin';
    case SUITE = 'suite';
    case DORMITORY = 'dormitory';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SINGLE => ucfirst('single'),
            self::DOUBLE => ucfirst('double'),
            self::TWIN => ucfirst('twin'),
            self::SUITE => ucfirst('suite'),
            self::DORMITORY => ucfirst('dormitory'),
        };
    }
}