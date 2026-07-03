<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquilino extends Model
{
    protected $fillable = [
        'unidad_habitacional_id',
        'nombre_completo',
        'documento',
        'telefono',
        'num_personas',
        'arrendador_nombre',
        'relacion_arrendador',
        'valor_arriendo',
        'tiempo_viviendo',
    ];

    protected function casts(): array
    {
        return ['valor_arriendo' => 'decimal:2'];
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadHabitacional::class, 'unidad_habitacional_id');
    }
}
