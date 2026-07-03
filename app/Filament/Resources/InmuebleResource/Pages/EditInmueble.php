<?php

namespace App\Filament\Resources\InmuebleResource\Pages;

use App\Filament\Resources\InmuebleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInmueble extends EditRecord
{
    protected static string $resource = InmuebleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
