<?php

namespace App\Filament\Resources\ExpenseBatches\Pages;

use App\Filament\Resources\ExpenseBatches\ExpenseBatchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExpenseBatches extends ListRecords
{
    protected static string $resource = ExpenseBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
