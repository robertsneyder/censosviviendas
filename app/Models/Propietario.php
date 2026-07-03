<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Propietario extends Model
{
    protected $fillable = [
        'inmueble_id',
        'nombre_completo',
        'documento',
        'telefono',
        'vive_en_inmueble',
        'lugar_residencia',
    ];

    protected function casts(): array
    {
        return ['vive_en_inmueble' => 'boolean'];
    }

    public function inmueble(): BelongsTo
    {
        return $this->belongsTo(Inmueble::class);
    }
}
