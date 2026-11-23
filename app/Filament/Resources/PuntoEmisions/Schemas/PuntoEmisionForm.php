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
                    ->required(),
                TextInput::make('punto_codigo')
                    ->required(),
                TextInput::make('secuencia_actual')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('descripcion'),
            ]);
    }
}
