<?php

namespace App\Models;

// Añade estas importaciones
use App\Enums\UserType;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
// ---
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Implementa la interfaz FilamentUser
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'type' => UserType::class, 
        ];
    }

    /**
     * Lógica para controlar el acceso al panel de Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Solo permite el acceso si el usuario es de tipo ADMINISTRATIVO y está activo.
        return $this->type === UserType::ADMINISTRATIVO && $this->is_active;
    }
}
