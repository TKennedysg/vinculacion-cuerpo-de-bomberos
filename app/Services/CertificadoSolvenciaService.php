<?php

namespace App\Services;

use App\Models\CertificadoSolvencia;
use App\Models\Comprobante;
use App\Models\Contribuyente;
use App\Models\Predio;
use App\Models\PuntoEmision;
use App\Models\TipoTramite;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Emisión de certificados de solvencia (RF-34 a RF-37).
 *
 * La persistencia se hace en una sola transacción: comprobante + certificado +
 * predios + formas de pago. TODO(Leo): cuando entregue `RegistroCobroService`,
 * la creación del comprobante y de las formas de pago se delega allí.
 */
class CertificadoSolvenciaService
{
    public const VALOR = '10.00';

    /** @var array<int, string> */
    public const ESTADOS_CERTIFICABLES = ['ACTIVO', 'INACTIVO'];

    public function __construct(private readonly SecuenciaTesoreriaService $secuenciaTesoreria) {}

    /**
     * @param  array{
     *     contribuyente_id: int,
     *     tipo_tramite_id: int,
     *     descripcion_tramite: string,
     *     a_favor_de: string,
     *     predio_ids: array<int, int|string>,
     *     punto_emision_id: int,
     *     usuario_id: int,
     *     observacion?: ?string
     * }  $datos
     * @param  array<int, array{forma_pago: string, valor: string|float, numero_documento?: ?string, banco?: ?string}>  $formasPago
     */
    public function emitir(array $datos, array $formasPago): CertificadoSolvencia
    {
        return DB::transaction(function () use ($datos, $formasPago): CertificadoSolvencia {
            $contribuyente = Contribuyente::query()->find($datos['contribuyente_id'] ?? null);

            if (! $contribuyente) {
                throw new InvalidArgumentException('El contribuyente seleccionado no existe.');
            }

            $tipoTramite = TipoTramite::query()->find($datos['tipo_tramite_id'] ?? null);

            if (! $tipoTramite) {
                throw new InvalidArgumentException('El tipo de trámite seleccionado no existe.');
            }

            $descripcion = trim((string) ($datos['descripcion_tramite'] ?? ''));
            $aFavorDe = trim((string) ($datos['a_favor_de'] ?? ''));

            if ($descripcion === '' || $aFavorDe === '') {
                throw new InvalidArgumentException('La descripción del trámite y el beneficiario son obligatorios.');
            }

            $predios = $this->resolverPredios($contribuyente, $datos['predio_ids'] ?? []);
            $this->validarFormasPago($formasPago);

            $puntoEmision = PuntoEmision::query()->find($datos['punto_emision_id'] ?? null);

            if (! $puntoEmision) {
                throw new InvalidArgumentException('El punto de emisión no existe.');
            }

            $secuencia = $this->secuenciaTesoreria->siguienteParaComprobante($puntoEmision);

            $comprobante = Comprobante::query()->create([
                'tipo' => 'SOLVENCIA',
                'punto_emision_id' => $secuencia['punto_emision_id'],
                'secuencial' => $secuencia['secuencial'],
                'fecha_emision' => now()->toDateString(),
                'contribuyente_id' => $contribuyente->getKey(),
                'usuario_id' => (int) $datos['usuario_id'],
                'valor_total' => self::VALOR,
                'observacion' => $this->normalizarObservacion($datos['observacion'] ?? null),
                'estado' => 'A',
            ]);

            $certificado = CertificadoSolvencia::query()->create([
                'comprobante_id' => $comprobante->getKey(),
                'tipo_tramite_id' => $tipoTramite->getKey(),
                'descripcion_tramite' => $descripcion,
                'a_favor_de' => $aFavorDe,
                'leyenda' => $this->leyenda($tipoTramite),
                'valor' => self::VALOR,
            ]);

            foreach ($predios as $predio) {
                $certificado->predios()->create([
                    'codigo_catastral' => $predio->codigo_catastral,
                    'matricula' => $predio->matricula,
                ]);
            }

            foreach ($formasPago as $forma) {
                $comprobante->formasPago()->create([
                    'forma_pago' => $forma['forma_pago'],
                    'valor' => $forma['valor'],
                    'numero_documento' => $forma['numero_documento'] ?? null,
                    'banco' => $forma['banco'] ?? null,
                ]);
            }

            return $certificado->refresh()->load([
                'comprobante.puntoEmision',
                'comprobante.contribuyente',
                'comprobante.usuario',
                'tipoTramite',
                'predios',
            ]);
        });
    }

    public function leyenda(TipoTramite $tipoTramite): string
    {
        return 'La persona interesada podrá hacer uso de la presente Certificación únicamente para tramitación de: '.$tipoTramite->nombre;
    }

    /**
     * @param  array<int, int|string>  $ids
     * @return Collection<int, Predio>
     */
    private function resolverPredios(Contribuyente $contribuyente, array $ids): Collection
    {
        $ids = array_values(array_unique(array_map('intval', is_array($ids) ? $ids : [])));

        if ($ids === []) {
            throw new InvalidArgumentException('Debe seleccionar al menos un predio.');
        }

        $predios = Predio::query()
            ->where('contribuyente_id', $contribuyente->getKey())
            ->whereIn('estado', self::ESTADOS_CERTIFICABLES)
            ->whereIn('id', $ids)
            ->get();

        if ($predios->count() !== count($ids)) {
            throw new InvalidArgumentException('Los predios deben pertenecer al contribuyente y estar activos o inactivos.');
        }

        return $predios;
    }

    /**
     * @param  array<int, array{forma_pago: string, valor: string|float, numero_documento?: ?string, banco?: ?string}>  $formasPago
     */
    private function validarFormasPago(array $formasPago): void
    {
        if ($formasPago === []) {
            throw new InvalidArgumentException('Debe registrar al menos una forma de pago.');
        }

        $total = 0.0;

        foreach ($formasPago as $forma) {
            $valor = round((float) ($forma['valor'] ?? 0), 2);

            if ($valor <= 0) {
                throw new InvalidArgumentException('Cada forma de pago debe tener un valor mayor a cero.');
            }

            $total += $valor;
        }

        if (round($total, 2) !== (float) self::VALOR) {
            throw new InvalidArgumentException('La suma de las formas de pago debe ser igual a $'.self::VALOR.'.');
        }
    }

    private function normalizarObservacion(?string $observacion): ?string
    {
        $observacion = trim((string) $observacion);

        return $observacion === '' ? null : $observacion;
    }
}
