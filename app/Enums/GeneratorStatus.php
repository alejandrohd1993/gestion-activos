<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum GeneratorStatus: string implements HasLabel, HasColor, HasIcon
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

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ACTIVO => 'success',
            self::EN_MANTENIMIENTO => 'warning',
            self::FUERA_DE_SERVICIO => 'gray',
            self::DADO_DE_BAJA => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ACTIVO => 'heroicon-o-check-circle',
            self::EN_MANTENIMIENTO => 'heroicon-o-wrench-screwdriver',
            self::FUERA_DE_SERVICIO => 'heroicon-o-exclamation-circle',
            self::DADO_DE_BAJA => 'heroicon-o-x-circle',
        };
    }
}
