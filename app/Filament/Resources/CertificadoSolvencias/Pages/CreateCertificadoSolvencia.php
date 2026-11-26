<?php

namespace App\Filament\Resources\CertificadoSolvencias\Pages;

use App\Filament\Resources\CertificadoSolvencias\CertificadoSolvenciaResource;
use App\Filament\Resources\CertificadoSolvencias\Schemas\CertificadoSolvenciaForm;
use App\Services\CertificadoSolvenciaService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCertificadoSolvencia extends CreateRecord
{
    protected static string $resource = CertificadoSolvenciaResource::class;

    public function getTitle(): string
    {
        return 'Emitir certificado de solvencia';
    }

    protected function handleRecordCreation(array $data): Model
    {
        $contribuyenteId = (int) $data['contribuyente_id'];
        $deuda = CertificadoSolvenciaForm::deuda($contribuyenteId);
        $anios = $deuda['anios'];
        $adeuda = $deuda['adeuda'];

        $observacion = $adeuda
            ? 'Se informó al contribuyente de la deuda predial'
                .($anios !== [] ? ' (años: '.implode(', ', $anios).')' : '')
                .' antes de emitir el certificado.'
            : null;

        // TODO(Leo): reemplazar por el modal de formas de pago cuando esté disponible.
        $formasPago = [[
            'forma_pago' => (string) $data['forma_pago'],
            'valor' => CertificadoSolvenciaService::VALOR,
            'numero_documento' => $data['numero_documento'] ?? null,
            'banco' => $data['banco'] ?? null,
        ]];

        $certificado = app(CertificadoSolvenciaService::class)->emitir([
            'contribuyente_id' => $contribuyenteId,
            'tipo_tramite_id' => (int) $data['tipo_tramite_id'],
            'descripcion_tramite' => (string) $data['descripcion_tramite'],
            'a_favor_de' => (string) $data['a_favor_de'],
            'predio_ids' => $data['predio_ids'] ?? [],
            'punto_emision_id' => (int) $data['punto_emision_id'],
            'usuario_id' => (int) auth()->id(),
            'observacion' => $observacion,
        ], $formasPago);

        return $certificado;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Certificado de solvencia emitido')
            ->body('Puede reimprimirlo desde el historial de solvencias.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
