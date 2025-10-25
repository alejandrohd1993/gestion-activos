<?php

namespace App\Models;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'provider_id',
        'user_id',
        'status',
        'type',
        'date',
        'maintainable_id',
        'maintainable_type',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => MaintenanceStatus::class,
            'date' => 'date',
            'type' => MaintenanceType::class,
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function maintainable(): MorphTo
    {
        return $this->morphTo();
    }

    public function components(): BelongsToMany
    {
        return $this->belongsToMany(Component::class)
            ->withPivot('quantity')
            ->withTimestamps();
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
