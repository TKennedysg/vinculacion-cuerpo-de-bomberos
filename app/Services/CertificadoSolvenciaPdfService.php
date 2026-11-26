<?php

namespace App\Services;

use App\Models\CertificadoSolvencia;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * TODO(Leo): reemplazar por la plantilla PDF común cuando esté disponible.
 */
class CertificadoSolvenciaPdfService
{
    public function descargar(CertificadoSolvencia $certificado): StreamedResponse
    {
        $certificado->load([
            'comprobante.puntoEmision',
            'comprobante.contribuyente',
            'comprobante.usuario',
            'tipoTramite',
            'predios',
        ]);

        $pdf = Pdf::loadView('pdf.certificado-solvencia', [
            'certificado' => $certificado,
        ])->setPaper('a4');

        $nombre = 'certificado-solvencia-'.($certificado->comprobante?->secuencial ?? $certificado->getKey()).'.pdf';

        return response()->streamDownload(
            function () use ($pdf): void {
                echo $pdf->output();
            },
            $nombre,
            ['Content-Type' => 'application/pdf'],
        );
    }
}
