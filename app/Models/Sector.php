<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    protected $table = 'sectores';

    protected $fillable = ['barrio_id', 'nombre', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class);
    }

    public function inmuebles(): HasMany
    {
        return $this->hasMany(Inmueble::class);
    }

    public function rutaTerritorial(): string
    {
        $barrio = $this->barrio;
        $comuna = $barrio?->comuna;
        $municipio = $comuna?->municipio;
        $departamento = $municipio?->departamento;

        return collect([
            $departamento?->nombre,
            $municipio?->nombre,
            $comuna?->nombre,
            $barrio?->nombre,
            $this->nombre,
        ])->filter()->implode(' > ');
    }
}
