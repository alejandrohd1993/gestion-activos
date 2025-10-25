<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Enums\AssetStatus;
use App\Enums\AssetType;
use App\Enums\UnitType;
use App\Models\Asset;
use App\Models\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Activo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Hidden::make('type')
                            ->default(AssetType::VEHICULO),
                        // Select::make('type') ... // Se elimina ya que ahora solo son vehículos

                        Select::make('status')
                            ->label('Estado')
                            ->options(AssetStatus::class)
                            ->required(),
                        TextInput::make('brand')
                            ->label('Marca')
                            ->maxLength(255),
                        TextInput::make('model')
                            ->label('Modelo')
                            ->maxLength(255),
                    ]),

                Section::make('Medidor Actual')
                    ->schema([
                        TextInput::make('current_meter')
                            ->label('Odómetro (Km)')
                            ->numeric()
                            ->required(),
                    ]),

                Section::make('Componentes a Vigilar')
                    ->schema([
                        Repeater::make('components')
                            ->label('Componentes')
                            ->columns(2)
                            ->schema([
                                Select::make('component_id')
                                    ->label('Componente')
                                    ->options(fn(): Collection => Component::where('category', 'vehiculo')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                DatePicker::make('last_maintenance_date')
                                    ->label('Fecha Último Mantenimiento')
                                    ->required()
                                    ->visible(function (Get $get) {
                                        if (!$componentId = $get('component_id')) return false;
                                        $component = Component::find($componentId);
                                        return $component && $component->unit->type === UnitType::CALENDARIO;
                                    }),

                                TextInput::make('last_maintenance_meter')
                                    ->label('Medidor Último Mantenimiento')
                                    ->required()
                                    ->numeric()
                                    ->visible(function (Get $get) {
                                        if (!$componentId = $get('component_id')) return false;
                                        $component = Component::find($componentId);
                                        return $component && $component->unit->type === UnitType::USO_ACUMULADO;
                                    }),
                            ])
                            ->defaultItems(0),
                    ]),
            ]);
    }
}
