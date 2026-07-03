<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogoGrupo extends Model
{
    protected $fillable = ['slug', 'nombre', 'descripcion', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function opciones(): HasMany
    {
        return $this->hasMany(CatalogoOpcion::class);
    }

    public static function opcionesPorSlug(string $slug): array
    {
        return static::query()
            ->where('slug', $slug)
            ->where('activo', true)
            ->first()
            ?->opciones()
            ->where('activo', true)
            ->orderBy('orden')
            ->pluck('etiqueta', 'valor')
            ->toArray() ?? [];
    }
}
