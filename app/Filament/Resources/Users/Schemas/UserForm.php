<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\PuntoEmision;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('perfil_id')
                    ->label('Perfil')
                    ->relationship('perfil', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nombres')
                    ->label('Nombres completos')
                    ->minLength(2)
                    ->maxLength(255)
                    ->required(),
                TextInput::make('correo')
                    ->label('Correo electrónico')
                    ->email()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('password_hash')
                    ->label('Contraseña')
                    ->password()
                    ->rule(Password::min(8)->mixedCase()->numbers()->symbols())
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                TextInput::make('password_confirmation')
                    ->label('Confirmar contraseña')
                    ->password()
                    ->same('password_hash')
                    ->required(fn (Get $get): bool => filled($get('password_hash')))
                    ->dehydrated(false),
                Select::make('contribuyente_id')
                    ->label('Contribuyente')
                    ->relationship('contribuyente', 'razon_social')
                    ->searchable()
                    ->preload(),
                Select::make('punto_emision_id')
                    ->label('Punto de emisión')
                    ->relationship('puntoEmision', 'id')
                    ->getOptionLabelFromRecordUsing(fn (PuntoEmision $record): string => "{$record->establecimiento_codigo}-{$record->punto_codigo}")
                    ->searchable()
                    ->preload(),
                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'ACTIVO' => 'ACTIVO',
                        'INACTIVO' => 'INACTIVO',
                    ])
                    ->native(false)
                    ->required()
                    ->default('ACTIVO'),
            ]);
    }
}
