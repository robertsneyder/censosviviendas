<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatalogoGrupoResource\Pages;
use App\Filament\Resources\CatalogoGrupoResource\RelationManagers;
use App\Models\CatalogoGrupo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CatalogoGrupoResource extends Resource
{
    protected static ?string $model = CatalogoGrupo::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Catálogos del censo';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('nombre')->required(),
            Forms\Components\Textarea::make('descripcion'),
            Forms\Components\Toggle::make('activo')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->searchable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('opciones_count')->counts('opciones')->label('Opciones'),
                Tables\Columns\IconColumn::make('activo')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OpcionesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCatalogoGrupos::route('/'),
            'edit' => Pages\EditCatalogoGrupo::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('super_admin') || $user->can('catalogos.gestionar'));
    }
}
