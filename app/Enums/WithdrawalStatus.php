<?php

namespace App\Enums;

enum WithdrawalStatus: string
{
    case EN_ATTENTE = 'en_attente';
    case PAYE = 'paye';
    case REJETE = 'rejete';

    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE => 'En attente',
            self::PAYE => 'Payé',
            self::REJETE => 'Rejeté',
        };
    }
}
