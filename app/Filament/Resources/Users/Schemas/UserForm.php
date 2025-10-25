<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->email()
                    ->required(),
                Select::make('type')
                    ->label('Tipo')
                    ->options(UserType::class)
                    ->default('operativo')
                    ->required(),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->default(null),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true)
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->label('Contraseña')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null) // solo guarda si hay valor
                    ->dehydrated(fn ($state) => filled($state)) // evita que se envíe vacío
                    ->required(fn (string $context): bool => $context === 'create'), // requerido solo en crear
            ]);
    }
}
