<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UnidadHabitacional extends Model
{
    protected $table = 'unidades_habitacionales';

    protected $fillable = [
        'inmueble_id',
        'identificacion',
        'tipo_unidad',
        'estado',
        'ocupante_nombre',
        'ocupante_documento',
        'ocupante_telefono',
        'calidad_ocupante',
        'arrendador_nombre',
        'arrendador_telefono',
    ];

    public function inmueble(): BelongsTo
    {
        return $this->belongsTo(Inmueble::class);
    }

    public function inquilino(): HasOne
    {
        return $this->hasOne(Inquilino::class);
    }

    public function estaArrendada(): bool
    {
        return $this->estado === 'arrendada';
    }
}
