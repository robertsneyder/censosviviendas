<?php

namespace App\Filament\Resources\InmuebleResource\Pages;

use App\Filament\Resources\InmuebleResource;
use App\Models\CatalogoGrupo;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewInmueble extends ViewRecord
{
    protected static string $resource = InmuebleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('editar_censo')
                ->label('Editar censo completo')
                ->icon('heroicon-o-pencil-square')
                ->url(fn () => route('censo.edit', $this->record)),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Inmueble')
                ->schema([
                    Infolists\Components\TextEntry::make('sector.rutaTerritorial')->label('Territorio'),
                    Infolists\Components\TextEntry::make('direccion'),
                    Infolists\Components\TextEntry::make('referencia_ubicacion')->label('Referencia'),
                    Infolists\Components\TextEntry::make('tipo_inmueble')
                        ->formatStateUsing(fn ($state) => CatalogoGrupo::opcionesPorSlug('tipo_inmueble')[$state] ?? $state),
                    Infolists\Components\TextEntry::make('estado_ocupacion')
                        ->formatStateUsing(fn ($state) => CatalogoGrupo::opcionesPorSlug('estado_ocupacion')[$state] ?? $state),
                    Infolists\Components\TextEntry::make('estado_completitud')->badge(),
                    Infolists\Components\IconEntry::make('requiere_nueva_visita')->boolean()->label('Requiere nueva visita'),
                    Infolists\Components\TextEntry::make('observaciones')->columnSpanFull(),
                ])->columns(2),
            Infolists\Components\Section::make('Propietario')
                ->schema([
                    Infolists\Components\TextEntry::make('propietario.nombre_completo')->label('Nombre'),
                    Infolists\Components\TextEntry::make('propietario.documento')->label('Documento'),
                    Infolists\Components\TextEntry::make('propietario.telefono')->label('Teléfono'),
                    Infolists\Components\IconEntry::make('propietario.vive_en_inmueble')->boolean()->label('Vive en el inmueble'),
                    Infolists\Components\TextEntry::make('propietario.lugar_residencia')->label('Lugar de residencia'),
                ])->columns(2)
                ->visible(fn ($record) => $record->propietario !== null),
            Infolists\Components\Section::make('Encargado')
                ->schema([
                    Infolists\Components\TextEntry::make('encargado.nombre_completo')->label('Nombre'),
                    Infolists\Components\TextEntry::make('encargado.documento')->label('Documento'),
                    Infolists\Components\TextEntry::make('encargado.telefono')->label('Teléfono'),
                    Infolists\Components\TextEntry::make('encargado.relacion_propietario')->label('Relación'),
                    Infolists\Components\IconEntry::make('encargado.vive_en_inmueble')->boolean()->label('Vive en el inmueble'),
                ])->columns(2)
                ->visible(fn ($record) => $record->encargado?->hay_encargado),
            Infolists\Components\RepeatableEntry::make('unidades')
                ->label('Unidades habitacionales')
                ->schema([
                    Infolists\Components\TextEntry::make('identificacion')->label('Identificación'),
                    Infolists\Components\TextEntry::make('tipo_unidad')->label('Tipo'),
                    Infolists\Components\TextEntry::make('estado')->label('Estado'),
                    Infolists\Components\TextEntry::make('ocupante_nombre')->label('Ocupante'),
                    Infolists\Components\TextEntry::make('inquilino.nombre_completo')->label('Inquilino'),
                ])->columns(3),
        ]);
    }
}
