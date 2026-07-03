<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'departamento_id',
        'municipio_id',
        'comuna_id',
        'barrio_id',
        'sector_id',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->activo && $this->hasAnyRole([
            'super_admin',
            'administrador',
            'coordinador',
            'censista',
            'consulta',
        ]);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }

    public function comuna(): BelongsTo
    {
        return $this->belongsTo(Comuna::class);
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function censos(): HasMany
    {
        return $this->hasMany(Inmueble::class, 'censista_id');
    }

    public function alcanceTerritorial(): ?string
    {
        if ($this->sector_id) {
            return $this->sector?->rutaTerritorial();
        }
        if ($this->barrio_id) {
            return $this->barrio?->nombre;
        }
        if ($this->comuna_id) {
            return $this->comuna?->nombre;
        }
        if ($this->municipio_id) {
            return $this->municipio?->nombre;
        }
        if ($this->departamento_id) {
            return $this->departamento?->nombre;
        }

        return 'Nacional';
    }
}
