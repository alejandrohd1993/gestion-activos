<?php

namespace App\Filament\Resources\Usages\Schemas;

use App\Models\Asset;
use App\Models\Expense;
use App\Models\Generator;
use App\Models\Maintenance;
use App\Models\Service;
use App\Models\Usage;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HorometerField;

class UsageForm
{
    public static function configure(Schema $schema): Schema
    {

        $start_date = request()->query('start_date');
        $end_date = request()->query('end_date');
        $usable_type = request()->query('usable_type');
        $usable_id = request()->query('usable_id');
        $equipment_type = request()->query('maintainable_type');
        $equipment_id = request()->query('maintainable_id');


        return $schema
            ->components([
                Section::make('Información General')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('date')
                            ->label('Fecha de Uso')
                            ->default($start_date)
                            ->minDate($start_date)
                            ->maxDate($end_date)
                            ->required(),

                        Select::make('usable_type')
                            ->label('Tipo de Uso')
                            ->default($usable_type)
                            ->options([
                                Service::class => 'Servicio',
                                Maintenance::class => 'Mantenimiento',
                            ])
                            ->live()
                            ->required(),

                        Select::make('usable_id')
                            ->label('Relacionado Con')
                            ->default($usable_id)
                            ->options(function (Get $get): array {
                                $type = $get('usable_type');
                                if (!$type) return [];
                                return $type::pluck('name', 'id')->all();
                            })
                            ->live()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->afterStateUpdated(fn (Set $set) => $set('equipment_id', null)),
                    ]),

                Section::make('Equipo y Medidores')
                    ->columns(2)
                    ->schema([
                        Select::make('equipment_type')
                            ->label('Tipo de Equipo')
                            ->options([
                                Asset::class => 'Activo',
                                Generator::class => 'Generador',
                            ])
                            ->live()
                            ->default($equipment_type)
                            ->required()
                            ->afterStateUpdated(function (Set $set) {
                                $set('equipment_id', null);
                                // Resetea los medidores al cambiar el tipo de equipo
                                $set('initial_meter', 0);
                                $set('final_meter', 0);
                                $set('total_usage', 0);
                            }),

                        Select::make('equipment_id')
                            ->label('Equipo Específico')
                            ->default($equipment_id)
                            ->options(function (Get $get): array {
                                $equipmentType = $get('equipment_type');
                                $usableType = $get('usable_type');
                                $usableId = $get('usable_id');

                                if (!$equipmentType) {
                                    return [];
                                }

                                // Si es un servicio, filtramos los equipos asociados a ese servicio.
                                if ($usableType === Service::class && $usableId) {
                                    $service = Service::find($usableId);
                                    if ($service) {
                                        /** @var \Illuminate\Database\Eloquent\Model $equipmentModel */
                                        $equipmentModel = new $equipmentType();
                                        $tableName = $equipmentModel->getTable();
                                        $relation = ($equipmentType === Asset::class) ? 'assets' : 'generators';
                                        return $service->{$relation}()->pluck("{$tableName}.name", "{$tableName}.id")->all();
                                    }
                                }

                                // Si es un mantenimiento, filtramos el equipo asociado a ese mantenimiento.
                                if ($usableType === Maintenance::class && $usableId) {
                                    // La relación correcta en el modelo Maintenance es 'maintainable'
                                    $maintenance = Maintenance::with('maintainable')->find($usableId);
                                    if ($maintenance && $maintenance->maintainable) {
                                        // Asegurarse de que el tipo de equipo del mantenimiento coincida con el seleccionado
                                        if ($maintenance->maintainable_type === $equipmentType) {
                                            return [$maintenance->maintainable->id => $maintenance->maintainable->name];
                                        }
                                        // Si el tipo de equipo no coincide, no mostramos nada.
                                        return [];
                                    }
                                }

                                // Si se ha seleccionado un usable_id pero no se ha encontrado coincidencia, no mostrar nada.
                                if ($usableId) return [];

                                return $equipmentType::pluck('name', 'id')->all();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                if (blank($state)) {
                                    $set('initial_meter', 0);
                                    return;
                                }

                                $equipmentType = $get('equipment_type');
                                $equipmentId = $state;

                                // Buscar el último uso para este equipo
                                $lastUsage = Usage::where('equipment_type', $equipmentType)
                                    ->where('equipment_id', $equipmentId)
                                    ->latest('date')
                                    ->latest('id')
                                    ->first();

                                // Obtener el valor en segundos.
                                $initialMeterInSeconds = $lastUsage ? $lastUsage->final_meter : $equipmentType::find($equipmentId)?->current_meter ?? 0;

                                // Si el equipo es un Generador, convertir los segundos a formato HH:MM:SS.
                                // De lo contrario, usar el valor numérico directamente (para odómetro).
                                if ($equipmentType === Generator::class) {
                                    $hours = floor($initialMeterInSeconds / 3600);
                                    $minutes = floor(($initialMeterInSeconds % 3600) / 60);
                                    $seconds = $initialMeterInSeconds % 60;
                                    $formattedTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                    $set('initial_meter', $formattedTime);
                                } else {
                                    $set('initial_meter', $initialMeterInSeconds);
                                }
                            }),

                        // Medidores para Activos (Kilómetros)
                        TextInput::make('initial_meter')
                            ->label('Odómetro Inicial (Km)')
                            ->numeric()
                            ->live(onBlur: true)
                            // ->afterStateUpdated(fn(Set $set, Get $get) => self::updateTotalUsage($set, $get))
                            ->visible(fn(Get $get) => $get('equipment_type') === Asset::class),

                        TextInput::make('final_meter')
                            ->label('Odómetro Final (Km)')
                            ->numeric()
                            ->live(onBlur: true)
                            // ->afterStateUpdated(fn(Set $set, Get $get) => self::updateTotalUsage($set, $get))
                            ->visible(fn(Get $get) => $get('equipment_type') === Asset::class),

                        // Medidores para Generadores (Horómetro)
                        HorometerField::makeHorometerField('initial_meter', 'Horómetro Inicial')
                            ->live(onBlur: true)
                            // ->afterStateUpdated(fn(Set $set, Get $get) => self::updateTotalUsage($set, $get))
                            ->visible(fn(Get $get) => $get('equipment_type') === Generator::class),

                        HorometerField::makeHorometerField('final_meter', 'Horómetro Final')
                            ->live(onBlur: true)
                            // ->afterStateUpdated(fn(Set $set, Get $get) => self::updateTotalUsage($set, $get))
                            ->visible(fn(Get $get) => $get('equipment_type') === Generator::class),

                        // TextInput::make('total_usage')
                        //     ->label(fn(Get $get) => $get('equipment_type') === Asset::class ? 'Km Recorridos' : 'Horas Trabajadas (segundos)')
                        //     ->numeric()
                        //     ->readOnly()
                        //     ->columnSpanFull(),
                    ]),

                Section::make('Notas')
                    ->schema([
                        TextInput::make('notes')
                            ->label('Notas Adicionales')
                            ->columnSpanFull(),
                    ]),

                Section::make('Registrar Gastos Asociados')
                    ->description('Añada los gastos específicos incurridos durante este uso del equipo.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('expense_items')
                            ->label('Gastos')
                            ->columns(2)
                            ->schema([
                                Select::make('expense_id')
                                    ->label('Tipo de Gasto')
                                    ->options(Expense::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                TextInput::make('amount')
                                    ->label('Monto')
                                    ->numeric()
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->prefix('$')
                                    ->required(),
                                Textarea::make('description')
                                    ->label('Descripción del Gasto')->columnSpanFull(),
                            ])
                            ->addActionLabel('Añadir Gasto')
                            ->dehydrated(false) // No guardaremos esto en la tabla 'usages'
                    ]),
            ]);
    }

     // Función auxiliar para calcular el uso total
    // protected static function updateTotalUsage(Set $set, Get $get): void
    // {
    //     $initial = (int) $get('initial_meter');
    //     $final = (int) $get('final_meter');
    //     $total = max(0, $final - $initial); // Asegura que no sea negativo
    //     $set('total_usage', $total);
    // }

}
