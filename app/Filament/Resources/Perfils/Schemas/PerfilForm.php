<?php

namespace App\Filament\Resources\Perfils\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PerfilForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('nombre')
                    ->options([
                        'ADMINISTRADOR' => 'ADMINISTRADOR',
                        'RECAUDADOR' => 'RECAUDADOR',
                        'CONTRIBUYENTE' => 'CONTRIBUYENTE',
                    ])
                    ->native(false)
                    ->required(),
                Textarea::make('descripcion')
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }
}
