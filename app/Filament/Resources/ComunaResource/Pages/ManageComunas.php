<?php

namespace App\Filament\Resources\ComunaResource\Pages;

use App\Filament\Resources\ComunaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageComunas extends ManageRecords
{
    protected static string $resource = ComunaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! empty($data['municipio_id'])) {
            $data['departamento_id'] = \App\Models\Municipio::find($data['municipio_id'])?->departamento_id;
        }

        return $data;
    }
}
