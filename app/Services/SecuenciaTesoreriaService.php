<?php

namespace App\Services;

use App\Models\PuntoEmision;
use DomainException;
use Illuminate\Support\Facades\DB;

class SecuenciaTesoreriaService
{
    public function siguiente(PuntoEmision $puntoEmision): string
    {
        return $this->siguienteDatos($puntoEmision)['numero'];
    }

    /**
     * @return array{punto_emision_id: int, secuencial: string, numero: string}
     */
    public function siguienteParaComprobante(PuntoEmision $puntoEmision): array
    {
        return $this->siguienteDatos($puntoEmision);
    }

    /**
     * @return array{punto_emision_id: int, secuencial: string, numero: string}
     */
    private function siguienteDatos(PuntoEmision $puntoEmision): array
    {
        return DB::transaction(function () use ($puntoEmision): array {
            /** @var PuntoEmision $puntoBloqueado */
            $puntoBloqueado = PuntoEmision::query()
                ->lockForUpdate()
                ->findOrFail($puntoEmision->getKey());

            $siguiente = $puntoBloqueado->secuencia_actual + 1;

            if ($siguiente > 999_999_999) {
                throw new DomainException('La secuencia de tesorería alcanzó su límite máximo.');
            }

            $puntoBloqueado->update([
                'secuencia_actual' => $siguiente,
            ]);

            $secuencial = str_pad((string) $siguiente, 9, '0', STR_PAD_LEFT);
            $numero = sprintf(
                '%s-%s-%s',
                $this->codigo($puntoBloqueado->establecimiento_codigo),
                $this->codigo($puntoBloqueado->punto_codigo),
                $secuencial,
            );

            return [
                'punto_emision_id' => (int) $puntoBloqueado->getKey(),
                'secuencial' => $secuencial,
                'numero' => $numero,
            ];
        });
    }

    private function codigo(string $codigo): string
    {
        if (! preg_match('/^\d{1,3}$/', $codigo)) {
            throw new DomainException('Los códigos de establecimiento y punto deben ser numéricos de hasta 3 dígitos.');
        }

        return str_pad($codigo, 3, '0', STR_PAD_LEFT);
    }
}
