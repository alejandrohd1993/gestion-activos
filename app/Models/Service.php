<?php

namespace App\Models;

use App\Enums\ServiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;


class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'customer_id',
        'user_id',
        'location',
        'status',
        'is_billed',
        'service_value',
        'start_date',
        'end_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ServiceStatus::class,
            'is_billed' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
            'service_value' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class);
    }

    public function generators(): BelongsToMany
    {
        return $this->belongsToMany(Generator::class);
    }

    /**
     * Relación polimórfica con Usos.
     */
    public function usages(): MorphMany
    {
        return $this->morphMany(Usage::class, 'usable');
    }

    /**
     * Relación polimórfica con Gastos.
     */
    public function expenseBatches(): MorphMany
    {
        return $this->morphMany(ExpenseBatch::class, 'expensable');
    }
}
