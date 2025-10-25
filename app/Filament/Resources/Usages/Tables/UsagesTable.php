<?php

namespace App\Filament\Resources\Usages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('equipment_type')
                    ->searchable(),
                TextColumn::make('equipment_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('usable_type')
                    ->searchable(),
                TextColumn::make('usable_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('initial_meter')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('final_meter')
                    ->numeric()
                    ->sortable(),
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
