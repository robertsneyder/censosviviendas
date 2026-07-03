<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogoOpcion extends Model
{
    protected $table = 'catalogo_opciones';

    protected $fillable = ['catalogo_grupo_id', 'valor', 'etiqueta', 'orden', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(CatalogoGrupo::class, 'catalogo_grupo_id');
    }
}
