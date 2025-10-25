<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Enums\ServiceStatus;
use App\Enums\UserType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Principal')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Servicio')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('customer_id')
                            ->label('Cliente')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('user_id')
                            ->label('Operador Asignado')
                            ->relationship(
                                name: 'operator',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn($query) => $query->where('type', UserType::OPERATIVO)
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('location')
                            ->label('Lugar')
                            ->maxLength(255),

                        Select::make('status')
                            ->label('Estado')
                            ->options(ServiceStatus::class)
                            ->required()
                            ->default(ServiceStatus::PENDIENTE),
                    ]),

                Section::make('Detalles Financieros y Fechas')
                    ->columns(2)
                    ->schema([
                        TextInput::make('service_value')
                            ->label('Valor del Servicio')
                            ->numeric()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->prefix('$')
                            ->required()
                            ->default(0),

                        Toggle::make('is_billed')
                            ->label('Facturado')
                            ->required(),

                        DatePicker::make('start_date')
                            ->label('Fecha de Inicio'),

                        DatePicker::make('end_date')
                            ->label('Fecha Final'),
                    ]),

                Section::make('Equipos Asignados')
                    ->columns(2)
                    ->schema([
                        Select::make('assets')
                            ->label('Activos Asignados')
                            ->relationship('assets', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),

                        Select::make('generators')
                            ->label('Generadores Asignados')
                            ->relationship('generators', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
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
