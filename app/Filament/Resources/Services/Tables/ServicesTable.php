<?php

namespace App\Filament\Resources\Services\Tables;

use App\Mail\ServiceCompletedForBilling;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use App\Enums\ServiceStatus;
use App\Filament\Resources\Usages\Schemas\UsageForm;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),
                IconColumn::make('is_billed')
                    ->label('Facturado')
                    ->boolean(),
                TextColumn::make('service_value')
                    ->label('Valor')
                    ->money('COP')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Fecha Inicio')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(ServiceStatus::class),
                SelectFilter::make('customer')
                    ->label('Cliente')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('registrarUso')
                        ->label('Registrar Uso')
                        ->icon('heroicon-o-clock')
                        ->color('info')
                        ->url(function ($record) {
                            $startDate = optional($record->start_date)->format('Y-m-d');
                            $endDate = optional($record->end_date)->format('Y-m-d');

                            $params = http_build_query([
                                'start_date' => $startDate,
                                'end_date' => $endDate,
                                'usable_type' => \App\Models\Service::class,
                                'usable_id' => $record->id,
                            ]);

                            return url("/admin/usages/create?$params");
                        }),
                    Action::make('registrarGasto')
                        ->label('Registrar Gastos')
                        ->icon('heroicon-o-currency-dollar')
                        ->action(function ($record) {

                            $startDate = optional($record->start_date)->format('Y-m-d');
                            $endDate = optional($record->end_date)->format('Y-m-d');

                            $params = http_build_query([
                                'expensable_type' => \App\Models\Service::class,
                                'expensable_id' => $record->id,
                                'start_date' => $startDate,
                                'end_date' => $endDate,
                                'scope' => 'general',
                            ]);

                            return redirect()->to(url("/admin/expense-batches/create?$params"));
                        }),
                    Action::make('marcarFacturado')
                        ->label('Marcar como Facturado')
                        ->icon('heroicon-o-receipt-percent')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn($record) => !$record->is_billed) // Solo mostrar si NO está facturado
                        ->action(function ($record, $livewire) {
                            $record->update(['is_billed' => true]);

                            // Muestra notificación
                            Notification::make()
                                ->title('Servicio marcado como facturado correctamente')
                                ->success()
                                ->send();
                        }),

                    Action::make('marcarCompletado')
                        ->label('Completado')
                        ->icon('heroicon-o-check-badge')
                        ->color('white')
                        ->visible(fn($record) => $record->status !== ServiceStatus::COMPLETADO)
                        ->action(function ($record) {
                            $record->update(['status' => ServiceStatus::COMPLETADO->value]);
 
                            Notification::make()
                                ->title('Servicio marcado como completado')
                                ->success()
                                ->send();

                            // Enviar correo a contabilidad
                            $accountingEmail = Setting::where('key', 'correo_contabilidad')->value('value');

                            if ($accountingEmail) {
                                Mail::to($accountingEmail)->send(new ServiceCompletedForBilling($record));
                            } else {
                                // Opcional: Notificar al admin si el correo no está configurado
                                Notification::make()->title('Correo de contabilidad no configurado')->warning()->sendToDatabase(Auth::user());
                            }
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
