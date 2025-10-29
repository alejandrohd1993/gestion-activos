<?php

namespace App\Filament\Resources\Components\Tables;

use App\Enums\ComponentCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ComponentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        \App\Enums\ComponentCategory::GENERADOR => 'success',
                        \App\Enums\ComponentCategory::VEHICULO => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('duration')
                    ->label('Vida Útil')
                    ->numeric()
                    ->formatStateUsing(function ($state, $record) {
                        // Si no hay unidad asociada, se devuelve el valor tal cual
                        if (! $record->unit) {
                            return $state;
                        }

                        // Si la unidad es "horas", convertir segundos a horas
                        if (strtolower($record->unit->name) === 'horas') {
                            return $state / 3600;
                        }

                        // De lo contrario, mostrar con la unidad original
                        return "{$state}";
                    })
                    ->sortable(),
                TextColumn::make('unit.name')
                    ->label('Unidad')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoría')
                    ->options(ComponentCategory::class),
                SelectFilter::make('unit_id')
                    ->label('Unidad de Medida')
                    ->relationship('unit', 'name')
                    ->preload()
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
