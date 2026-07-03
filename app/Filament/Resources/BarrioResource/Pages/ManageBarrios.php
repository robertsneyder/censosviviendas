<?php

namespace App\Filament\Resources\BarrioResource\Pages;

use App\Filament\Resources\BarrioResource;
use App\Models\Comuna;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBarrios extends ManageRecords
{
    protected static string $resource = BarrioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! empty($data['comuna_id'])) {
            $comuna = Comuna::with('municipio')->find($data['comuna_id']);
            $data['municipio_id'] = $comuna?->municipio_id;
            $data['departamento_id'] = $comuna?->municipio?->departamento_id;
        }

        return $data;
    }
}
