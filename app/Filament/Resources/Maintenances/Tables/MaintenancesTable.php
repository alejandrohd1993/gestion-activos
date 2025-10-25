<?php

namespace App\Filament\Resources\Maintenances\Tables;

use App\Enums\MaintenanceStatus;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MaintenancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('maintainable.name')
                    ->label('Equipo'),
                TextColumn::make('operator.name')
                    ->label('Operador')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(MaintenanceStatus::class),

            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('registrarUso')
                        ->label('Registrar Uso')
                        ->icon('heroicon-o-clock')
                        ->color('info')
                        ->url(function ($record) {
                            $date = optional($record->date)->format('Y-m-d');

                            $params = http_build_query([
                                'start_date' => $date,
                                'usable_type' => \App\Models\Maintenance::class,
                                'usable_id' => $record->id,
                                'maintainable_type' => $record->maintainable_type,
                                'maintainable_id' => $record->maintainable_id,
                            ]);

                            return url("/admin/usages/create?$params");
                        }),
                    Action::make('marcarCompletado')
                        ->label('Completado')
                        ->icon('heroicon-o-check-badge')
                        ->color('white')
                        ->visible(fn($record) => $record->status === MaintenanceStatus::PENDIENTE)
                        ->action(function ($record) {
                            $record->update(['status' => MaintenanceStatus::COMPLETADO->value]);

                            Notification::make()
                                ->title('Mantenimiento marcado como completado')
                                ->success()
                                ->send();
                        }),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
