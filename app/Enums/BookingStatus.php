<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BookingStatus: string implements HasLabel
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CHECKED_IN = 'checked_in';
    case CHECKED_OUT = 'checked_out';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => ucfirst('pending'),
            self::CONFIRMED => ucfirst('confirmed'),
            self::CHECKED_IN => ucfirst('checked_in'),
            self::CHECKED_OUT => ucfirst('checked_out'),
            self::CANCELLED => ucfirst('cancelled'),
            self::NO_SHOW => ucfirst('no_show'),
        };
    }
}