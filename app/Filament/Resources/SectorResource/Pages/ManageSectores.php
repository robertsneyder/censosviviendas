<?php

namespace App\Filament\Resources\SectorResource\Pages;

use App\Filament\Resources\SectorResource;
use App\Models\Barrio;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSectores extends ManageRecords
{
    protected static string $resource = SectorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! empty($data['barrio_id'])) {
            $barrio = Barrio::with('comuna.municipio')->find($data['barrio_id']);
            $data['comuna_id'] = $barrio?->comuna_id;
            $data['municipio_id'] = $barrio?->comuna?->municipio_id;
            $data['departamento_id'] = $barrio?->comuna?->municipio?->departamento_id;
        }

        return $data;
    }
}
