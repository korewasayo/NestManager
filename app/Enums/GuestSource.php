<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum GuestSource: string implements HasLabel
{
    case DIRECT = 'direct';
    case AIRBNB = 'airbnb';
    case BOOKING = 'booking';
    case OTHER = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DIRECT => ucfirst('direct'),
            self::AIRBNB => ucfirst('airbnb'),
            self::BOOKING => ucfirst('booking'),
            self::OTHER => ucfirst('other'),
        };
    }
}