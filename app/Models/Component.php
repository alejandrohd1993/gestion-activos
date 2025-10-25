<?php

namespace App\Models;

use App\Enums\ComponentCategory; // Importar Enum
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Component extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'unit_id',
        'duration',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'category' => ComponentCategory::class, // Castear al Enum
        'duration' => 'integer',
    ];

    /**
     * Define la relación: Un Componente pertenece a una Unidad de Medida.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class)
            ->withPivot('last_maintenance_meter', 'last_maintenance_date')
            ->withTimestamps();
    }

    public function generators(): BelongsToMany
    {
        return $this->belongsToMany(Generator::class)
            ->withPivot('last_maintenance_meter', 'last_maintenance_date')
            ->withTimestamps();
    }

    /**
     * Relación muchos a muchos con Mantenimientos.
     */
    public function maintenances(): BelongsToMany
    {
        return $this->belongsToMany(Maintenance::class)->withPivot('quantity');
    }
}
