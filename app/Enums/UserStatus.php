<?php

namespace App\Enums;

enum UserStatus: string
{
    case EN_ATTENTE_VALIDATION = 'en_attente_validation';
    case VALIDE = 'valide';
    case SUSPENDU = 'suspendu';

    public function label(): string
    {
        return match ($this) {
            self::EN_ATTENTE_VALIDATION => 'En attente de validation',
            self::VALIDE => 'Validé',
            self::SUSPENDU => 'Suspendu',
        };
    }
}
