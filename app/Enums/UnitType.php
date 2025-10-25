<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UnitType: string implements HasLabel
{
    case USO_ACUMULADO = 'uso_acumulado';
    case CALENDARIO = 'calendario';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::USO_ACUMULADO => 'Uso Acumulado',
            self::CALENDARIO => 'Calendario',
        };
    }
}
