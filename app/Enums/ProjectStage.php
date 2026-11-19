<?php

namespace App\Enums;

enum ProjectStage: string
{
    case ENREGISTREMENT = 'enregistrement';
    case MIXAGE = 'mixage';
    case MASTERING = 'mastering';
    case DISTRIBUTION_EN_COURS = 'distribution_en_cours';
    case DISTRIBUTION_TERMINEE = 'distribution_terminee';
    case DISTRIBUTION_DIRECTE = 'distribution_directe';

    public function label(): string
    {
        return match ($this) {
            self::ENREGISTREMENT => 'Enregistrement',
            self::MIXAGE => 'Mixage',
            self::MASTERING => 'Mastering',
            self::DISTRIBUTION_EN_COURS => 'Distribution en cours',
            self::DISTRIBUTION_TERMINEE => 'Distribution terminée',
            self::DISTRIBUTION_DIRECTE => 'Distribution directe',
        };
    }
}
