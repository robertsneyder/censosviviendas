<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipio extends Model
{
    protected $fillable = ['departamento_id', 'nombre', 'codigo', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function comunas(): HasMany
    {
        return $this->hasMany(Comuna::class);
    }
}
