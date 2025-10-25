<?php

namespace App\Filament\Resources\ExpenseBatches\Pages;

use App\Filament\Resources\ExpenseBatches\ExpenseBatchResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExpenseBatch extends EditRecord
{
    protected static string $resource = ExpenseBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
