<?php

namespace App\Filament\Resources\InmuebleResource\Pages;

use App\Filament\Resources\InmuebleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInmueble extends CreateRecord
{
    protected static string $resource = InmuebleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['censista_id'] = auth()->id();
        $data['fecha_registro'] = now();

        return $data;
    }
}
