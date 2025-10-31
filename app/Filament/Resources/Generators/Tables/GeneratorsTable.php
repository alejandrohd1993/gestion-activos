<?php

namespace App\Filament\Resources\Generators\Tables;

use App\Enums\UnitType;
use App\Models\Generator;
use App\Services\MaintenanceStatusService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GeneratorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('brand')
                    ->label('Marca')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->searchable(),
                TextColumn::make('id') // Cambiamos a un atributo que no sea una relación para evitar JOINs
                    ->label('Estado de Componentes') // La etiqueta sigue siendo la misma
                    ->html() // Para renderizar el HTML que generaremos
                    ->formatStateUsing(function ($state, Generator $record, MaintenanceStatusService $statusService) {
                        // Función para formatear segundos a HH:MM:SS
                        $formatSeconds = function (int $value): string {
                            $isNegative = $value < 0;
                            $absoluteValue = abs($value);
                            $hours = floor($absoluteValue / 3600);
                            $minutes = floor(($absoluteValue % 3600) / 60);
                            $seconds = $absoluteValue % 60;
                            return ($isNegative ? '-' : '') . sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                        };

                        $componentStatuses = $statusService->getComponentsStatus($record);

                        if ($componentStatuses->isEmpty()) {
                            return '<span class="text-xs text-gray-500">Sin componentes</span>';
                        }

                        $html = '<ul class="list-disc pl-4 space-y-1">';

                        foreach ($componentStatuses as $status) {
                            $color = match ($status->status) {
                                'expired' => 'text-danger-600',
                                'warning' => 'text-warning-600',
                                default => 'text-gray-700',
                            };

                            $html .= "<li><strong class=\"{$color}\">{$status->component_name}</strong>";

                            // Mostrar detalles solo para componentes basados en uso
                            if ($status->unit_type === UnitType::USO_ACUMULADO->value && isset($status->current_meter)) {
                                $last = $formatSeconds((int) $status->last_maintenance_meter);
                                $current = $formatSeconds((int) $status->current_meter);
                                $remaining = $formatSeconds((int) $status->usage_remaining);

                                $html .= "<div class=\"text-xs text-gray-500\">";
                                $html .= "Últ. Mant: {$last} | Actual: {$current} | <strong class=\"{$color}\">Restante: {$remaining}</strong>";
                                $html .= "<br>";
                                $html .= "<br>";
                                $html .= "</div>";
                            }

                            // Mostrar detalles para componentes basados en calendario
                            if ($status->unit_type === UnitType::CALENDARIO->value && isset($status->days_remaining)) {
                                $last = $status->last_maintenance_date;
                                $next = $status->next_maintenance_date;
                                $remaining = $status->days_remaining;
                                $html .= "<div class=\"text-xs text-gray-500\">";
                                $html .= "Últ. Mant: {$last} | Próximo: {$next} | <strong class=\"{$color}\">Restante: {$remaining} días</strong>";
                                $html .= "</div>";
                            }
                            $html .= '</li>';
                        }
                        $html .= '</ul>';
                        return $html;
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
