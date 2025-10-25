<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ServiceStatus: string implements HasLabel, HasColor, HasIcon
{
    case PENDIENTE = 'pendiente';
    case EN_EJECUCION = 'en_ejecucion';
    case COMPLETADO = 'completado';
    case CANCELADO = 'cancelado';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDIENTE => 'Pendiente',
            self::EN_EJECUCION => 'En Ejecución',
            self::COMPLETADO => 'Completado',
            self::CANCELADO => 'Cancelado',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PENDIENTE => 'gray',
            self::EN_EJECUCION => 'warning',
            self::COMPLETADO => 'success',
            self::CANCELADO => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDIENTE => 'heroicon-o-clock',
            self::EN_EJECUCION => 'heroicon-o-cog',
            self::COMPLETADO => 'heroicon-o-check-circle',
            self::CANCELADO => 'heroicon-o-x-circle',
        };
    }
}
