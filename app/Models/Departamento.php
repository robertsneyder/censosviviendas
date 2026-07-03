<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    protected $fillable = ['nombre', 'codigo', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class);
    }
}
