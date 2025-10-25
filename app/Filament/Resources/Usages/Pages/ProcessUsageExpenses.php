<?php

namespace App\Filament\Resources\Usages\Pages;

use App\Models\ExpenseBatch;
use App\Models\Usage;

trait ProcessUsageExpenses
{
    /**
     * Procesa y guarda los gastos asociados a un registro de uso.
     *
     * @param Usage $usageRecord El registro de uso recién creado.
     * @param array $expenseItems Los datos de los gastos desde el formulario.
     */
    protected function processExpenses(Usage $usageRecord, array $expenseItems): void
    {
        if (empty($expenseItems)) {
            return;
        }

        // Crear un lote de gastos. El gasto desde un Uso siempre es 'específico'.
        $expenseBatch = ExpenseBatch::create([
            'date' => $usageRecord->date,
            'expensable_type' => $usageRecord->usable_type,
            'expensable_id' => $usageRecord->usable_id,
            'scope' => 'specific', // Desde un uso, el gasto siempre es específico al equipo.
            'equipment_type' => $usageRecord->equipment_type,
            'equipment_id' => $usageRecord->equipment_id,
        ]);

        // Preparar los registros de gastos para la inserción en lote.
        $expenseRecords = collect($expenseItems)->map(function ($item) use ($expenseBatch) {
            return [
                'expense_batch_id' => $expenseBatch->id,
                'expense_id' => $item['expense_id'],
                'amount' => is_string($item['amount']) ? (float) str_replace(',', '', $item['amount']) : $item['amount'],
                'description' => $item['description'],
            ];
        })->all();

        // Crear los registros de gastos asociados al lote.
        $expenseBatch->expenseItems()->createMany($expenseRecords);
    }
}
