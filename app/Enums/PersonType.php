<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PersonType: string implements HasLabel
{
    case NATURAL = 'natural';
    case JURIDICA = 'juridica';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NATURAL => 'Natural',
            self::JURIDICA => 'Jurídica',
        };
    }
}
