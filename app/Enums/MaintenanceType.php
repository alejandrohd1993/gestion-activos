<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MaintenanceType: string implements HasLabel
{
    case PREVENTIVO = 'preventivo';
    case CORRECTIVO = 'correctivo';
    case PREDICTIVO = 'predictivo';
    case OTRO = 'otro';


    public function getLabel(): ?string
    {
        return match ($this) {
            self::PREVENTIVO => 'Preventivo',
            self::CORRECTIVO => 'Correctivo',
            self::PREDICTIVO => 'Predictivo',
            self::OTRO => 'Otro',
        };
    }
}
