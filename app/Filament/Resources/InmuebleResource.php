<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InmuebleResource\Pages;
use App\Models\CatalogoGrupo;
use App\Models\Inmueble;
use App\Models\Sector;
use App\Services\TerritorioService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InmuebleResource extends Resource
{
    protected static ?string $model = Inmueble::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Inmuebles';

    protected static ?string $modelLabel = 'Inmueble';

    protected static ?string $pluralModelLabel = 'Inmuebles';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identificación territorial')
                ->schema([
                    Forms\Components\Select::make('sector_id')
                        ->label('Sector')
                        ->options(fn () => Sector::with('barrio')->get()->mapWithKeys(
                            fn (Sector $s) => [$s->id => $s->rutaTerritorial()]
                        ))
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('direccion')->label('Dirección')->required()->maxLength(255),
                    Forms\Components\TextInput::make('referencia_ubicacion')->label('Referencia de ubicación')->maxLength(255),
                ])->columns(1),
            Forms\Components\Section::make('Datos del inmueble')
                ->schema([
                    Forms\Components\Select::make('tipo_inmueble')
                        ->label('Tipo de inmueble')
                        ->options(CatalogoGrupo::opcionesPorSlug('tipo_inmueble'))
                        ->required(),
                    Forms\Components\Select::make('estado_ocupacion')
                        ->label('Estado de ocupación')
                        ->options(CatalogoGrupo::opcionesPorSlug('estado_ocupacion'))
                        ->required(),
                    Forms\Components\Select::make('estado_completitud')
                        ->label('Estado del censo')
                        ->options(CatalogoGrupo::opcionesPorSlug('estado_completitud'))
                        ->required(),
                    Forms\Components\Toggle::make('requiere_nueva_visita')->label('Requiere nueva visita'),
                    Forms\Components\Textarea::make('observaciones')->label('Observaciones')->rows(3),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('direccion')->label('Dirección')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('sector.nombre')->label('Sector')->sortable(),
                Tables\Columns\TextColumn::make('tipo_inmueble')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => CatalogoGrupo::opcionesPorSlug('tipo_inmueble')[$state] ?? $state),
                Tables\Columns\TextColumn::make('estado_completitud')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completo' => 'success',
                        'parcial' => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\IconColumn::make('requiere_nueva_visita')->label('Nueva visita')->boolean(),
                Tables\Columns\TextColumn::make('censista.name')->label('Censista'),
                Tables\Columns\TextColumn::make('fecha_registro')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sector_id')
                    ->label('Sector')
                    ->relationship('sector', 'nombre'),
                Tables\Filters\SelectFilter::make('estado_completitud')
                    ->label('Estado del censo')
                    ->options(CatalogoGrupo::opcionesPorSlug('estado_completitud')),
                Tables\Filters\TernaryFilter::make('requiere_nueva_visita')->label('Requiere nueva visita'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_registro', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['sector', 'censista']);

        if ($user = auth()->user()) {
            return app(TerritorioService::class)->filtrarInmueblesPorUsuario($query, $user);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInmuebles::route('/'),
            'create' => Pages\CreateInmueble::route('/create'),
            'view' => Pages\ViewInmueble::route('/{record}'),
            'edit' => Pages\EditInmueble::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('inmuebles.ver') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('inmuebles.crear') ?? false;
    }
}
