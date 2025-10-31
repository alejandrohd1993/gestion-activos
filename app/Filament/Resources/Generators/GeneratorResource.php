<?php

namespace App\Filament\Resources\Generators;

use App\Filament\Resources\Generators\Pages\CreateGenerator;
use App\Filament\Resources\Generators\Pages\EditGenerator;
use App\Filament\Resources\Generators\Pages\ListGenerators;
use App\Filament\Resources\Generators\Schemas\GeneratorForm;
use App\Filament\Resources\Generators\Tables\GeneratorsTable;
use App\Models\Generator;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GeneratorResource extends Resource
{
    protected static ?string $model = Generator::class;

    protected static ?string $modelLabel = 'Generador';

    protected static ?string $pluralModelLabel = 'Generadores';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static ?int $navigationSort = 2;

    protected static string|\UnitEnum|null $navigationGroup = 'Equipos';

    public static function form(Schema $schema): Schema
    {
        return GeneratorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GeneratorsTable::configure($table);
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
            'index' => ListGenerators::route('/'),
            'create' => CreateGenerator::route('/create'),
            'edit' => EditGenerator::route('/{record}/edit'),
        ];
    }
}
