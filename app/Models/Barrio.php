<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barrio extends Model
{
    protected $fillable = ['comuna_id', 'nombre', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function comuna(): BelongsTo
    {
        return $this->belongsTo(Comuna::class);
    }

    public function sectores(): HasMany
    {
        return $this->hasMany(Sector::class);
    }
}
