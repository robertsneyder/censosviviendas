<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inmueble extends Model
{
    protected $fillable = [
        'sector_id',
        'direccion',
        'referencia_ubicacion',
        'tipo_inmueble',
        'estado_ocupacion',
        'observaciones',
        'censista_id',
        'fecha_registro',
        'estado_completitud',
        'requiere_nueva_visita',
    ];

    protected function casts(): array
    {
        return [
            'fecha_registro' => 'datetime',
            'requiere_nueva_visita' => 'boolean',
        ];
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function censista(): BelongsTo
    {
        return $this->belongsTo(User::class, 'censista_id');
    }

    public function propietario(): HasOne
    {
        return $this->hasOne(Propietario::class);
    }

    public function encargado(): HasOne
    {
        return $this->hasOne(Encargado::class);
    }

    public function unidades(): HasMany
    {
        return $this->hasMany(UnidadHabitacional::class);
    }
}
