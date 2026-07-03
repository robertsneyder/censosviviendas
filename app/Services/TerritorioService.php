<?php

namespace App\Services;

use App\Models\Inmueble;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TerritorioService
{
    public function filtrarInmueblesPorUsuario(Builder $query, User $user): Builder
    {
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->sector_id) {
            return $query->where('sector_id', $user->sector_id);
        }

        if ($user->barrio_id) {
            return $query->whereHas('sector', fn (Builder $q) => $q->where('barrio_id', $user->barrio_id));
        }

        if ($user->comuna_id) {
            return $query->whereHas('sector.barrio', fn (Builder $q) => $q->where('comuna_id', $user->comuna_id));
        }

        if ($user->municipio_id) {
            return $query->whereHas('sector.barrio.comuna', fn (Builder $q) => $q->where('municipio_id', $user->municipio_id));
        }

        if ($user->departamento_id) {
            return $query->whereHas('sector.barrio.comuna.municipio', fn (Builder $q) => $q->where('departamento_id', $user->departamento_id));
        }

        return $query;
    }

    public function usuarioPuedeAccederInmueble(User $user, Inmueble $inmueble): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        $inmueble->loadMissing('sector.barrio.comuna.municipio');

        if ($user->sector_id) {
            return $inmueble->sector_id === $user->sector_id;
        }

        if ($user->barrio_id) {
            return $inmueble->sector?->barrio_id === $user->barrio_id;
        }

        if ($user->comuna_id) {
            return $inmueble->sector?->barrio?->comuna_id === $user->comuna_id;
        }

        if ($user->municipio_id) {
            return $inmueble->sector?->barrio?->comuna?->municipio_id === $user->municipio_id;
        }

        if ($user->departamento_id) {
            return $inmueble->sector?->barrio?->comuna?->municipio?->departamento_id === $user->departamento_id;
        }

        return false;
    }
}
