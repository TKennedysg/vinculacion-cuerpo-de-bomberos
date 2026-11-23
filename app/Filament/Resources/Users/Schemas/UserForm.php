<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\PuntoEmision;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

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
                    ->required(),
                TextInput::make('correo')
                    ->label('Correo electrónico')
                    ->email()
                    ->required(),
                TextInput::make('password_hash')
                    ->label('Contraseña')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
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
                TextInput::make('estado')
                    ->label('Estado')
                    ->required()
                    ->default('ACTIVO'),
            ]);
    }
}
