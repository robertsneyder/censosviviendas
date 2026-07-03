<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComunaResource\Pages;
use App\Models\Comuna;
use App\Models\Departamento;
use App\Models\Municipio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ComunaResource extends Resource
{
    protected static ?string $model = Comuna::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Comunas';

    protected static ?string $modelLabel = 'comuna';

    protected static ?string $pluralModelLabel = 'comunas';

    protected static ?string $navigationGroup = 'Catálogo territorial';

    protected static ?int $navigationSort = 3;

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
                Tables\Columns\TextColumn::make('municipio.nombre')->label('Municipio')->sortable(),
                Tables\Columns\TextColumn::make('municipio.departamento.nombre')->label('Departamento'),
                Tables\Columns\TextColumn::make('barrios_count')->counts('barrios')->label('Barrios'),
                Tables\Columns\IconColumn::make('activo')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('municipio')
                    ->relationship('municipio', 'nombre'),
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
            'index' => Pages\ManageComunas::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('super_admin') || $user->can('territorio.gestionar'));
    }
}
