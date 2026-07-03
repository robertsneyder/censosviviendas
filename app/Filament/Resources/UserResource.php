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
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                        ->label('Contraseña')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create'),
                    Forms\Components\Select::make('roles')
                        ->label('Roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->required(),
                    Forms\Components\Toggle::make('activo')->label('Activo')->default(true),
                ])->columns(2),
            Forms\Components\Section::make('Alcance territorial')
                ->description('El nivel más específico define qué datos puede ver el usuario.')
                ->schema([
                    Forms\Components\Select::make('departamento_id')
                        ->label('Departamento')
                        ->options(Departamento::orderBy('nombre')->pluck('nombre', 'id'))
                        ->live()
                        ->afterStateUpdated(fn (Forms\Set $set) => $set('municipio_id', null)),
                    Forms\Components\Select::make('municipio_id')
                        ->label('Municipio')
                        ->options(fn (Get $get): array => $get('departamento_id')
                            ? Municipio::where('departamento_id', $get('departamento_id'))->orderBy('nombre')->pluck('nombre', 'id')->all()
                            : [])
                        ->live()
                        ->afterStateUpdated(fn (Forms\Set $set) => $set('comuna_id', null)),
                    Forms\Components\Select::make('comuna_id')
                        ->label('Comuna')
                        ->options(fn (Get $get): array => $get('municipio_id')
                            ? Comuna::where('municipio_id', $get('municipio_id'))->orderBy('nombre')->pluck('nombre', 'id')->all()
                            : [])
                        ->live()
                        ->afterStateUpdated(fn (Forms\Set $set) => $set('barrio_id', null)),
                    Forms\Components\Select::make('barrio_id')
                        ->label('Barrio')
                        ->options(fn (Get $get): array => $get('comuna_id')
                            ? Barrio::where('comuna_id', $get('comuna_id'))->orderBy('nombre')->pluck('nombre', 'id')->all()
                            : [])
                        ->live()
                        ->afterStateUpdated(fn (Forms\Set $set) => $set('sector_id', null)),
                    Forms\Components\Select::make('sector_id')
                        ->label('Sector')
                        ->options(fn (Get $get): array => $get('barrio_id')
                            ? Sector::where('barrio_id', $get('barrio_id'))->orderBy('nombre')->pluck('nombre', 'id')->all()
                            : []),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('roles.name')->badge()->label('Roles'),
                Tables\Columns\TextColumn::make('alcance')
                    ->label('Alcance')
                    ->getStateUsing(fn (User $record): string => $record->alcanceTerritorial() ?? '—'),
                Tables\Columns\IconColumn::make('activo')->boolean()->label('Activo'),
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
        $user = auth()->user();

        return $user && ($user->hasRole('super_admin') || $user->can('usuarios.gestionar'));
    }
}
