<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Encargado extends Model
{
    protected $fillable = [
        'inmueble_id',
        'hay_encargado',
        'nombre_completo',
        'documento',
        'telefono',
        'relacion_propietario',
        'vive_en_inmueble',
    ];

    protected function casts(): array
    {
        return [
            'hay_encargado' => 'boolean',
            'vive_en_inmueble' => 'boolean',
        ];
    }

    public function inmueble(): BelongsTo
    {
        return $this->belongsTo(Inmueble::class);
    }
}
