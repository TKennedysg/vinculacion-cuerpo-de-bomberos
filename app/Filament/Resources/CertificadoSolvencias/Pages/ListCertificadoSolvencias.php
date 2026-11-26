<?php

namespace App\Filament\Resources\CertificadoSolvencias\Pages;

use App\Filament\Resources\CertificadoSolvencias\CertificadoSolvenciaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCertificadoSolvencias extends ListRecords
{
    protected static string $resource = CertificadoSolvenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Emitir solvencia'),
        ];
    }
}
