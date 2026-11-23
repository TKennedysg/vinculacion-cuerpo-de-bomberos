<?php

namespace App\Filament\Resources\PuntoEmisions\Pages;

use App\Filament\Resources\PuntoEmisions\PuntoEmisionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPuntoEmisions extends ListRecords
{
    protected static string $resource = PuntoEmisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
