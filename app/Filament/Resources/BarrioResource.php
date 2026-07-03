<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarrioResource\Pages;
use App\Models\Barrio;
use App\Models\Comuna;
use App\Models\Departamento;
use App\Models\Municipio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BarrioResource extends Resource
{
    protected static ?string $model = Barrio::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Barrios';

    protected static ?string $modelLabel = 'barrio';

    protected static ?string $pluralModelLabel = 'barrios';

    protected static ?string $navigationGroup = 'Catálogo territorial';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('departamento_id')
                ->label('Departamento')
                ->options(Departamento::orderBy('nombre')->pluck('nombre', 'id'))
                ->live()
                ->dehydrated(false)
                ->afterStateUpdated(fn (Forms\Set $set) => $set('municipio_id', null)),
            Forms\Components\Select::make('municipio_id')
                ->label('Municipio')
                ->options(fn (Get $get): array => $get('departamento_id')
                    ? Municipio::where('departamento_id', $get('departamento_id'))->orderBy('nombre')->pluck('nombre', 'id')->all()
                    : [])
                ->live()
                ->dehydrated(false)
                ->afterStateUpdated(fn (Forms\Set $set) => $set('comuna_id', null)),
            Forms\Components\Select::make('comuna_id')
                ->label('Comuna')
                ->options(fn (Get $get): array => $get('municipio_id')
                    ? Comuna::where('municipio_id', $get('municipio_id'))->orderBy('nombre')->pluck('nombre', 'id')->all()
                    : [])
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('nombre')->label('Nombre')->required()->maxLength(255),
            Forms\Components\Toggle::make('activo')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('comuna.nombre')->label('Comuna')->sortable(),
                Tables\Columns\TextColumn::make('comuna.municipio.nombre')->label('Municipio'),
                Tables\Columns\TextColumn::make('sectores_count')->counts('sectores')->label('Sectores'),
                Tables\Columns\IconColumn::make('activo')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('comuna')
                    ->relationship('comuna', 'nombre'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBarrios::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('super_admin') || $user->can('territorio.gestionar'));
    }
}
