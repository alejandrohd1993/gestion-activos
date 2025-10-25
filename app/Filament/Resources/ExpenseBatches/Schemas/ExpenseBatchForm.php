<?php

namespace App\Filament\Resources\ExpenseBatches\Schemas;

use App\Models\Asset;
use App\Models\Component;
use App\Models\Generator;
use App\Models\Maintenance;
use App\Models\Service;
// use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ExpenseBatchForm
{
    public static function configure(Schema $schema): Schema
    {

        $start_date = request()->query('start_date');
        $end_date = request()->query('end_date');
        $scope = request()->query('scope');
        $expensable_type = request()->query('expensable_type');
        $expensable_id = request()->query('expensable_id');

        return $schema
            ->components([

                Hidden::make('equipment_type'),
                Hidden::make('equipment_id'),

                Section::make('Detalles de los Gastos')
                    ->schema([
                        DatePicker::make('date')
                            ->label('Fecha del Gasto')
                            ->required()
                            ->default(now() ? $start_date : null)
                            ->minDate($start_date)
                            ->maxDate($end_date),
                        Select::make('expensable_type')
                            ->label('Relacionado con')
                            ->default($expensable_type)
                            ->options([
                                Service::class => 'Servicio',
                                Maintenance::class => 'Mantenimiento',
                            ])
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('expensable_id', null))
                            ->required(),

                        Select::make('expensable_id')
                            ->label('ID del Servicio/Mantenimiento')
                            ->options(function (Get $get): array {
                                $type = $get('expensable_type');
                                if (!$type) return [];
                                return $type::query()->pluck('name', 'id')->all();
                            })
                            ->default($expensable_id)
                            ->live()
                            ->searchable()
                            ->preload()
                            ->required(),

                        Radio::make('scope')
                            ->label('Alcance del Gasto')
                            ->options([
                                'general' => 'General (Aplica a todo el servicio/mantenimiento)',
                                'specific' => 'Específico (Aplica a un equipo en particular)',
                            ])
                            ->default($scope ?? 'general')
                            ->live()
                            ->required(),

                        Select::make('equipment_id_composite')
                            ->label('Equipo Específico')
                            ->options(function (Get $get): Collection {
                                $expensableType = $get('expensable_type');
                                $expensableId = $get('expensable_id');

                                if (!$expensableType || !$expensableId) return collect();
                                $expensable = $expensableType::find($expensableId);
                                if (!$expensable) return collect();

                                $options = collect();
                                if ($expensable instanceof Service) {
                                    $assets = collect();
                                    $generators = collect();

                                    if (method_exists($expensable, 'assets')) {
                                        $assets = collect($expensable->assets?->mapWithKeys(fn($asset) => [
                                            Asset::class . '-' . $asset->id => $asset->name . ' (Activo)',
                                        ]));
                                    }

                                    if (method_exists($expensable, 'generators')) {
                                        $generators = collect($expensable->generators?->mapWithKeys(fn($gen) => [
                                            Generator::class . '-' . $gen->id => $gen->name . ' (Generador)',
                                        ]));
                                    }

                                    $options = $assets->merge($generators);
                                } elseif ($expensable instanceof Maintenance) {
                                    $maintainable = $expensable->maintainable;
                                    if ($maintainable) {
                                        $key = get_class($maintainable) . '-' . $maintainable->id;
                                        $label = $maintainable->name . ' (' . (get_class($maintainable) === Asset::class ? 'Activo' : 'Generador') . ')';
                                        $options->put($key, $label);
                                    }
                                }
                                return $options;
                            })
                            ->searchable()
                            ->live()
                            ->requiredIf('scope', 'specific')
                            ->visible(fn(Get $get) => $get('scope') === 'specific')
                            ->dehydrated(false)
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (empty($state)) return;

                                [$type, $id] = explode('-', $state);
                                $set('equipment_type', $type);
                                $set('equipment_id', $id);
                            })
                            ->afterStateHydrated(function ($state, $record, Set $set) {
                                if ($record && $record->equipment_type && $record->equipment_id) {
                                    $set('equipment_id_composite', "{$record->equipment_type}-{$record->equipment_id}");
                                }
                            }),
                    ]),

                // Este es el cambio más importante: usamos un RelationManager
                Repeater::make('expenseItems')
                    ->relationship()
                    ->label('Registrar Gastos')
                    
                    ->schema([
                        Select::make('expense_id')
                            ->label('Tipo de Gasto')
                            ->relationship('expense', 'name', fn(Builder $query) => $query->where('is_active', true))
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
                            ->label('Descripción')
                            ->columnSpanFull(),
                    ])
                    ->defaultItems(1)
                    ->minItems(1)
                    ->addActionLabel('Añadir otro gasto')
                    ,
            ]);
    }
}
