<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PropertyStatus: string implements HasLabel
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case MAINTENANCE = 'maintenance';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => ucfirst('active'),
            self::INACTIVE => ucfirst('inactive'),
            self::MAINTENANCE => ucfirst('maintenance'),
        };
    }
}