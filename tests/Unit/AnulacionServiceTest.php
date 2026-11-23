<?php

namespace Tests\Unit;

use App\Models\Comprobante;
use App\Models\PuntoEmision;
use App\Services\AnulacionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use LogicException;
use Tests\TestCase;

class AnulacionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_anulates_an_active_document_and_records_audit_data(): void
    {
        $puntoEmision = PuntoEmision::query()->create([
            'establecimiento_codigo' => '001',
            'punto_codigo' => '007',
        ]);
        $comprobante = Comprobante::query()->create([
            'tipo' => 'PREDIAL',
            'punto_emision_id' => $puntoEmision->id,
            'secuencial' => '000000001',
            'fecha_emision' => now()->toDateString(),
            'usuario_id' => 10,
            'valor_total' => 1.25,
        ]);

        $resultado = app(AnulacionService::class)->anular($comprobante, 'Cobro registrado por error', 10);

        $this->assertSame('X', $resultado->estado);
        $this->assertSame('Cobro registrado por error', $resultado->motivo_anulacion);
        $this->assertSame(10, $resultado->anulado_por);
        $this->assertNotNull($resultado->anulado_en);
    }

    public function test_rejects_an_empty_reason(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(AnulacionService::class)->anular(new Comprobante, '  ', 10);
    }

    public function test_rejects_an_already_cancelled_document(): void
    {
        $puntoEmision = PuntoEmision::query()->create([
            'establecimiento_codigo' => '001',
            'punto_codigo' => '007',
        ]);
        $comprobante = Comprobante::query()->create([
            'tipo' => 'PREDIAL',
            'punto_emision_id' => $puntoEmision->id,
            'secuencial' => '000000001',
            'fecha_emision' => now()->toDateString(),
            'usuario_id' => 10,
            'valor_total' => 1.25,
            'estado' => 'X',
        ]);

        $this->expectException(LogicException::class);

        app(AnulacionService::class)->anular($comprobante, 'Segundo intento', 10);
    }
}
