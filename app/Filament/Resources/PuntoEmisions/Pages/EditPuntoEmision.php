<?php

namespace App\Filament\Resources\PuntoEmisions\Pages;

use App\Filament\Resources\PuntoEmisions\PuntoEmisionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPuntoEmision extends EditRecord
{
    protected static string $resource = PuntoEmisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
