<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Enums\PersonType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nit')
                    ->label('NIT / Cédula')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('name')
                    ->label('Nombre o Razón Social')
                    ->required()
                    ->maxLength(255),
                Select::make('person_type')
                    ->label('Tipo de Persona')
                    ->options(PersonType::class)
                    ->required(),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('address')
                    ->label('Dirección'),
            ]);
    }
}
