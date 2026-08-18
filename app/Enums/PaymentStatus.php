<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => ucfirst('pending'),
            self::COMPLETED => ucfirst('completed'),
            self::REFUNDED => ucfirst('refunded'),
            self::FAILED => ucfirst('failed'),
        };
    }
}