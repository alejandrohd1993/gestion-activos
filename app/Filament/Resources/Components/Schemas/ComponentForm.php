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
                    ->numeric()
                    ->required()
                    // 👇 Mostrar en horas si la unidad es "horas"
                    ->formatStateUsing(function ($state, Get $get) {
                        $unit = Unit::find($get('unit_id'));
                        if ($state && $unit && strtolower($unit->name) === 'horas') {
                            return $state / 3600;
                        }
                        return $state;
                    })
                    // 👇 Convertir a segundos al guardar si la unidad es "horas"
                    ->dehydrateStateUsing(function ($state, Get $get) {
                        $unit = Unit::find($get('unit_id'));
                        if ($state && $unit && strtolower($unit->name) === 'horas') {
                            return $state * 3600;
                        }
                        return $state;
                    })
                    // 👇 Mostrar el sufijo dinámico
                    ->suffix(fn(Get $get): ?string => $get('unit_id') ? Unit::find($get('unit_id'))->name : null),
            ]);
    }
}
