<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SectorResource\Pages;
use App\Models\Barrio;
use App\Models\Sector;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SectorResource extends Resource
{
    protected static ?string $model = Sector::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Sectores';

    protected static ?string $navigationGroup = 'Territorio';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('barrio_id')
                ->label('Barrio')
                ->options(Barrio::pluck('nombre', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('nombre')->required()->maxLength(255),
            Forms\Components\Toggle::make('activo')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('barrio.nombre')->label('Barrio'),
                Tables\Columns\TextColumn::make('barrio.comuna.nombre')->label('Comuna'),
                Tables\Columns\TextColumn::make('inmuebles_count')->counts('inmuebles')->label('Inmuebles'),
                Tables\Columns\IconColumn::make('activo')->boolean(),
            ])
            ->filters([])
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
            'index' => Pages\ManageSectores::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('territorio.gestionar') ?? false;
    }
}
