<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AssetType: string implements HasLabel
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
