<?php

namespace App\Filament\Resources\ExpenseBatches\Pages;

use App\Filament\Resources\ExpenseBatches\ExpenseBatchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenseBatch extends CreateRecord
{
    protected static string $resource = ExpenseBatchResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
