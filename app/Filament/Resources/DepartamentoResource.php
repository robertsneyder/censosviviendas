<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartamentoResource\Pages;
use App\Models\Departamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DepartamentoResource extends Resource
{
    protected static ?string $model = Departamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-americas';

    protected static ?string $navigationLabel = 'Departamentos';

    protected static ?string $modelLabel = 'departamento';

    protected static ?string $pluralModelLabel = 'departamentos';

    protected static ?string $navigationGroup = 'Catálogo territorial';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
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
                Tables\Columns\TextColumn::make('codigo')->label('Código'),
                Tables\Columns\TextColumn::make('municipios_count')->counts('municipios')->label('Municipios'),
                Tables\Columns\IconColumn::make('activo')->boolean(),
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
            'index' => Pages\ManageDepartamentos::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('super_admin') || $user->can('territorio.gestionar'));
    }
}
