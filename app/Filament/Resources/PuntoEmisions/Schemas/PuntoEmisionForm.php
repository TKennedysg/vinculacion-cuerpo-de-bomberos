<?php

namespace App\Filament\Resources\PuntoEmisions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PuntoEmisionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('establecimiento_codigo')
                    ->label('Código de establecimiento')
                    ->minLength(3)
                    ->maxLength(3)
                    ->regex('/^\d{3}$/')
                    ->required(),
                TextInput::make('punto_codigo')
                    ->label('Código de punto')
                    ->minLength(3)
                    ->maxLength(3)
                    ->regex('/^\d{3}$/')
                    ->required(),
                TextInput::make('secuencia_actual')
                    ->required()
                    ->integer()
                    ->minValue(0)
                    ->maxValue(999999999)
                    ->default(0),
                TextInput::make('descripcion')
                    ->maxLength(255),
            ]);
    }
}
