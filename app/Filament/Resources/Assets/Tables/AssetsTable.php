<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Enums\AssetStatus;
use App\Enums\AssetType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable(),
                TextColumn::make('type')->label('Tipo')->badge(),
                TextColumn::make('brand')->label('Marca')->searchable(),
                TextColumn::make('model')->label('Modelo')->searchable(),
                TextColumn::make('status')->label('Estado'),

            ])
            ->filters([
                SelectFilter::make('type')->label('Tipo')->options(AssetType::class),
                SelectFilter::make('status')->label('Estado')->options(AssetStatus::class),
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
