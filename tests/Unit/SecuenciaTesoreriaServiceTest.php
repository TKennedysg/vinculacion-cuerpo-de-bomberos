<?php

namespace Tests\Unit;

use App\Models\PuntoEmision;
use App\Services\SecuenciaTesoreriaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecuenciaTesoreriaServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_the_first_treasury_sequence(): void
    {
        $puntoEmision = PuntoEmision::query()->create([
            'establecimiento_codigo' => '001',
            'punto_codigo' => '007',
            'secuencia_actual' => 0,
        ]);

        $resultado = app(SecuenciaTesoreriaService::class)->siguiente($puntoEmision);

        $this->assertSame('001-007-000000001', $resultado);
        $this->assertDatabaseHas('punto_emision', [
            'id' => $puntoEmision->id,
            'secuencia_actual' => 1,
        ]);
    }

    public function test_increments_the_sequence_without_reusing_numbers(): void
    {
        $puntoEmision = PuntoEmision::query()->create([
            'establecimiento_codigo' => '1',
            'punto_codigo' => '7',
            'secuencia_actual' => 41,
        ]);

        $servicio = app(SecuenciaTesoreriaService::class);

        $this->assertSame('001-007-000000042', $servicio->siguiente($puntoEmision));
        $this->assertSame('001-007-000000043', $servicio->siguiente($puntoEmision));
    }

    public function test_returns_separated_values_for_a_comprobante(): void
    {
        $puntoEmision = PuntoEmision::query()->create([
            'establecimiento_codigo' => '001',
            'punto_codigo' => '007',
            'secuencia_actual' => 8,
        ]);

        $resultado = app(SecuenciaTesoreriaService::class)->siguienteParaComprobante($puntoEmision);

        $this->assertSame([
            'punto_emision_id' => $puntoEmision->id,
            'secuencial' => '000000009',
            'numero' => '001-007-000000009',
        ], $resultado);
    }
}
