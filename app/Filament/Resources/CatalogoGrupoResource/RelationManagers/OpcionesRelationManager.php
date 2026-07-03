<?php

namespace App\Filament\Resources\CatalogoGrupoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OpcionesRelationManager extends RelationManager
{
    protected static string $relationship = 'opciones';

    protected static ?string $title = 'Opciones';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('valor')->required(),
            Forms\Components\TextInput::make('etiqueta')->required(),
            Forms\Components\TextInput::make('orden')->numeric()->default(0),
            Forms\Components\Toggle::make('activo')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('orden')->sortable(),
                Tables\Columns\TextColumn::make('valor'),
                Tables\Columns\TextColumn::make('etiqueta'),
                Tables\Columns\IconColumn::make('activo')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
