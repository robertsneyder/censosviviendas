<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MunicipioResource\Pages;
use App\Models\Departamento;
use App\Models\Municipio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MunicipioResource extends Resource
{
    protected static ?string $model = Municipio::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'Municipios';

    protected static ?string $modelLabel = 'municipio';

    protected static ?string $pluralModelLabel = 'municipios';

    protected static ?string $navigationGroup = 'Catálogo territorial';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('departamento_id')
                ->label('Departamento')
                ->options(Departamento::orderBy('nombre')->pluck('nombre', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('nombre')->label('Nombre')->required()->maxLength(255),
            Forms\Components\TextInput::make('codigo')->label('Código')->maxLength(10),
            Forms\Components\Toggle::make('activo')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('departamento.nombre')->label('Departamento')->sortable(),
                Tables\Columns\TextColumn::make('codigo')->label('Código'),
                Tables\Columns\TextColumn::make('comunas_count')->counts('comunas')->label('Comunas'),
                Tables\Columns\IconColumn::make('activo')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('departamento_id')
                    ->label('Departamento')
                    ->options(Departamento::orderBy('nombre')->pluck('nombre', 'id')),
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
            'index' => Pages\ManageMunicipios::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('super_admin') || $user->can('territorio.gestionar'));
    }
}
