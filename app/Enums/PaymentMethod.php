<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case MBWAY = 'mbway';
    case CARD = 'card';
    case OTHER = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CASH => ucfirst('cash'),
            self::BANK_TRANSFER => ucfirst('bank_transfer'),
            self::MBWAY => ucfirst('mbway'),
            self::CARD => ucfirst('card'),
            self::OTHER => ucfirst('other'),
        };
    }
}