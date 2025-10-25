<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AssetStatus: string implements HasLabel
{
    case ACTIVO = 'activo';
    case EN_MANTENIMIENTO = 'en_mantenimiento';
    case FUERA_DE_SERVICIO = 'fuera_de_servicio';
    case DADO_DE_BAJA = 'dado_de_baja';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVO => 'Activo',
            self::EN_MANTENIMIENTO => 'En Mantenimiento',
            self::FUERA_DE_SERVICIO => 'Fuera de Servicio',
            self::DADO_DE_BAJA => 'Dado de Baja',
        };
    }
}
