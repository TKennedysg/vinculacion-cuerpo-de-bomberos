<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;

class AnulacionService
{
    public function anular(Model $documento, string $motivo, int $usuarioId): Model
    {
        $motivo = trim($motivo);

        if ($motivo === '') {
            throw new InvalidArgumentException('El motivo de anulación es obligatorio.');
        }

        return DB::transaction(function () use ($documento, $motivo, $usuarioId): Model {
            $documentoBloqueado = $documento->newQuery()
                ->lockForUpdate()
                ->findOrFail($documento->getKey());

            if ($documentoBloqueado->getAttribute('estado') !== 'A') {
                throw new LogicException('El documento ya no está activo.');
            }

            $documentoBloqueado->forceFill([
                'estado' => 'X',
                'motivo_anulacion' => $motivo,
                'anulado_por' => $usuarioId,
                'anulado_en' => now(),
            ])->save();

            return $documentoBloqueado->refresh();
        });
    }
}
