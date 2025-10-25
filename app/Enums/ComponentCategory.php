<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ComponentCategory: string implements HasLabel
{
    case VEHICULO = 'vehiculo';
    case GENERADOR = 'generador';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::VEHICULO => 'Vehículo',
            self::GENERADOR => 'Generador',
        };
    }
}
