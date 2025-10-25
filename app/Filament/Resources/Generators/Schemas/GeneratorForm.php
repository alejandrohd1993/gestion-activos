<?php

namespace App\Filament\Resources\Generators\Schemas;

use App\Enums\GeneratorStatus;
use App\Enums\UnitType;
use App\Models\Component;
use Dom\Text;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use App\Traits\HorometerField;

class GeneratorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información General')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Generador')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('status')
                            ->label('Estado')
                            ->options(GeneratorStatus::class)
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
                        HorometerField::makeHorometerField('current_meter', 'Horómetro Actual'),
                    ]),


                Section::make('Componentes a Vigilar')
                    ->schema([
                        Repeater::make('components')
                            ->label('Componentes')
                            ->columns(2)
                            ->schema([
                                Select::make('component_id')
                                    ->label('Componente')
                                    ->options(fn(): Collection => Component::where('category', 'generador')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                DatePicker::make('last_maintenance_date')
                                    ->label('Fecha Último Mto')
                                    ->required()
                                    ->visible(function (Get $get) {
                                        if (!$componentId = $get('component_id')) return false;
                                        $component = Component::find($componentId);
                                        return $component && $component->unit->type === UnitType::CALENDARIO;
                                    }),

                                HorometerField::makeHorometerField('last_maintenance_meter', 'Horómetro Último Mto')
                                    ->visible(function (Get $get) {
                                        if (! $componentId = $get('component_id')) {
                                            return false;
                                        }
                                        $component = Component::find($componentId);
                                        return $component && $component->unit->type === UnitType::USO_ACUMULADO;
                                    }),
                            ])
                            ->defaultItems(0),
                    ]),
            ]);
    }
}
