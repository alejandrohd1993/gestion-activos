<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseRecord extends Model
{
    use HasFactory;

    // Los campos comunes se han movido a ExpenseBatch
    protected $fillable = [
        'expense_batch_id',
        'expense_id',
        'amount',
        'description',
    ];

    public function expenseBatch(): BelongsTo
    {
        return $this->belongsTo(ExpenseBatch::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
