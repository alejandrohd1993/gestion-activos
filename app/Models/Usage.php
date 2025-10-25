<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Usage extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'equipment_id',
        'equipment_type',
        'usable_id',
        'usable_type',
        'initial_meter',
        'final_meter',
        // 'total_usage',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /**
     * Relación polimórfica: un uso pertenece a un equipo (Activo o Generador).
     */
    public function equipment(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relación polimórfica: un uso puede estar asociado a un Servicio o Mantenimiento.
     */
    public function usable(): MorphTo
    {
        return $this->morphTo();
    }
}
