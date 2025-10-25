<?php

namespace App\Filament\Resources\Maintenances\Schemas;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Enums\UserType;
use App\Models\Asset;
use App\Models\Component;
use App\Models\Generator;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class MaintenanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Mantenimiento')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre / Título')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('provider_id')
                            ->label('Proveedor')
                            ->relationship('provider', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('user_id')
                            ->label('Operador Asignado')
                            ->relationship(
                                name: 'operator',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->where('type', UserType::OPERATIVO)
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('status')
                            ->label('Estado')
                            ->options(MaintenanceStatus::class)
                            ->required()
                            ->default(MaintenanceStatus::PENDIENTE),

                        Select::make('type')
                            ->label('Tipo de Mantenimiento')
                            ->options(MaintenanceType::class)
                            ->required(),

                        DatePicker::make('date')
                            ->label('Fecha del Mantenimiento')
                            ->required()
                            ->default(now()),
                    ]),

                Section::make('Equipo a Mantener')
                    ->schema([
                        Select::make('maintainable_type')
                            ->label('Tipo de Equipo')
                            ->options([
                                Asset::class => 'Activo',
                                Generator::class => 'Generador',
                            ])
                            ->live()
                            ->required(),

                        Select::make('maintainable_id')
                            ->label('Equipo Específico')
                            ->options(function (Get $get): array {
                                $type = $get('maintainable_type');
                                if (!$type) {
                                    return [];
                                }
                                return $type::query()->pluck('name', 'id')->all();
                            })
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                    ]),

                Section::make('Insumos Utilizados')
                    ->schema([
                        Repeater::make('components')
                            ->label('Componentes / Insumos')
                            ->schema([
                                Select::make('component_id')
                                    ->label('Componente')
                                    ->options(function (Get $get): array {
                                        $maintainableType = $get('../../maintainable_type');
                                        $maintainableId = $get('../../maintainable_id');

                                        if (!$maintainableType || !$maintainableId) {
                                            return [];
                                        }

                                        $model = $maintainableType::find($maintainableId);

                                        return $model ? $model->components()->pluck('components.name', 'components.id')->all() : [];
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            ])
                            ->defaultItems(0),
                    ]),

                Section::make('Notas Adicionales')
                    ->schema([
                        TextInput::make('notes')
                            ->label('Notas')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
