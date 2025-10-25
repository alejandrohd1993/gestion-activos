<?php

namespace App\Filament\Resources\Components\Schemas;

use App\Enums\ComponentCategory;
use App\Models\Unit;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ComponentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre del Insumo')
                    ->required()
                    ->maxLength(255),
                Select::make('category')
                    ->label('Categoría')
                    ->options(ComponentCategory::class)
                    ->required(),
                Select::make('unit_id')
                    ->label('Unidad de Medida')
                    ->relationship('unit', 'name')
                    ->preload()
                    ->live()
                    ->required(),
                TextInput::make('duration')
                    ->label('Duración / Vida Útil')
                    ->required()
                    ->numeric()
                    // Sufijo dinámico que muestra la unidad seleccionada
                    ->suffix(fn (Get $get): ?string => $get('unit_id') ? Unit::find($get('unit_id'))->name : null),
            ]);
    }
}
