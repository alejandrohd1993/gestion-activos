<?php

namespace App\Filament\Resources\ExpenseBatches;

use App\Filament\Resources\ExpenseBatches\Pages\CreateExpenseBatch;
use App\Filament\Resources\ExpenseBatches\Pages\EditExpenseBatch;
use App\Filament\Resources\ExpenseBatches\Pages\ListExpenseBatches;
use App\Filament\Resources\ExpenseBatches\Schemas\ExpenseBatchForm;
use App\Filament\Resources\ExpenseBatches\Tables\ExpenseBatchesTable;
use App\Models\ExpenseBatch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExpenseBatchResource extends Resource
{
    protected static ?string $model = ExpenseBatch::class;

    protected static ?string $modelLabel = 'Registro de Gasto';
    
    protected static ?string $pluralModelLabel = 'Registros de Gastos';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-currency-dollar';

    public static function form(Schema $schema): Schema
    {
        return ExpenseBatchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpenseBatchesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExpenseBatches::route('/'),
            'create' => CreateExpenseBatch::route('/create'),
            'edit' => EditExpenseBatch::route('/{record}/edit'),
        ];
    }
}
