<?php

namespace App\Filament\Resources\InmuebleResource\Pages;

use App\Filament\Resources\InmuebleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInmuebles extends ListRecords
{
    protected static string $resource = InmuebleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('nuevo_censo')
                ->label('Nuevo censo (formulario)')
                ->icon('heroicon-o-clipboard-document-check')
                ->url(route('censo.create'))
                ->visible(fn (): bool => auth()->user()?->can('censos.crear') ?? false),
            Actions\CreateAction::make(),
        ];
    }
}
