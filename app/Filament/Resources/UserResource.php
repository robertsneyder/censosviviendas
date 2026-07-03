<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Barrio;
use App\Models\Comuna;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Sector;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Usuarios';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Datos de acceso')
                ->schema([
                    Forms\Components\TextInput::make('name')->label('Nombre')->required(),
                    Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create'),
                    Forms\Components\Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->options(Role::pluck('name', 'id')),
                    Forms\Components\Toggle::make('activo')->default(true),
                ])->columns(2),
            Forms\Components\Section::make('Alcance territorial')
                ->description('El nivel más específico define qué datos puede ver el usuario.')
                ->schema([
                    Forms\Components\Select::make('departamento_id')
                        ->label('Departamento')
                        ->options(Departamento::pluck('nombre', 'id'))
                        ->live(),
                    Forms\Components\Select::make('municipio_id')
                        ->label('Municipio')
                        ->options(fn (Forms\Get $get) => Municipio::where('departamento_id', $get('departamento_id'))->pluck('nombre', 'id'))
                        ->live(),
                    Forms\Components\Select::make('comuna_id')
                        ->label('Comuna')
                        ->options(fn (Forms\Get $get) => Comuna::where('municipio_id', $get('municipio_id'))->pluck('nombre', 'id'))
                        ->live(),
                    Forms\Components\Select::make('barrio_id')
                        ->label('Barrio')
                        ->options(fn (Forms\Get $get) => Barrio::where('comuna_id', $get('comuna_id'))->pluck('nombre', 'id'))
                        ->live(),
                    Forms\Components\Select::make('sector_id')
                        ->label('Sector')
                        ->options(fn (Forms\Get $get) => Sector::where('barrio_id', $get('barrio_id'))->pluck('nombre', 'id')),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('roles.name')->badge()->label('Roles'),
                Tables\Columns\TextColumn::make('alcanceTerritorial')->label('Alcance'),
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('usuarios.gestionar') ?? false;
    }
}
