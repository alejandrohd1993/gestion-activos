<?php

namespace App\Models;

use App\Enums\GeneratorStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\ExpenseRecord;

class Generator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'model',
        'status',
        'current_meter',
    ];

    protected function casts(): array
    {
        return [
            'status' => GeneratorStatus::class,
        ];
    }

    public function components(): BelongsToMany
    {
        return $this->belongsToMany(Component::class)
            ->withPivot('last_maintenance_meter', 'last_maintenance_date')
            ->withTimestamps();
    }

    /**
     * Relación muchos a muchos con Servicios.
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    /**
     * Relación polimórfica con Mantenimientos.
     */
    public function maintenances(): MorphMany
    {
        return $this->morphMany(Maintenance::class, 'maintainable');
    }

    /**
     * Relación polimórfica con Usos.
     */
    public function usages(): MorphMany
    {
        return $this->morphMany(Usage::class, 'equipment');
    }

    /**
     * Relación polimórfica con Gastos.
     */
    public function expenseBatches(): MorphMany
    {
        return $this->morphMany(ExpenseBatch::class, 'equipment');
    }
}
