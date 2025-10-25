<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ExpenseBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'expensable_type',
        'expensable_id',
        'scope',
        'equipment_type',
        'equipment_id',
    ];

    /**
     * Obtiene todos los items de gasto para este lote.
     */
    public function expenseItems(): HasMany
    {
        return $this->hasMany(ExpenseRecord::class);
    }

    /**
     * Obtiene el modelo padre al que se le puede asignar el gasto (Service o Maintenance).
     */
    public function expensable(): MorphTo
    {
        return $this->morphTo();
    }

    public function equipment(): MorphTo
    {
        return $this->morphTo();
    }
}
