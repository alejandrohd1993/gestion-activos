<?php

namespace App\Filament\Resources\Usages\Pages;

use App\Filament\Resources\Usages\UsageResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Usage;

class CreateUsage extends CreateRecord
{
    use ProcessUsageExpenses;

    protected static string $resource = UsageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        // Obtener los datos de los gastos del formulario.
        $expenseItems = $this->data['expense_items'] ?? [];

        // Procesar y guardar los gastos usando el Trait.
        $this->processExpenses($this->getRecord(), $expenseItems);
    }
}
