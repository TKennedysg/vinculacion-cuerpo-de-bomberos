<?php

namespace Tests\Unit;

use App\Models\Comprobante;
use App\Models\Perfil;
use App\Models\PuntoEmision;
use App\Models\User;
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
        $usuarioId = $this->crearUsuario()->id;
        $puntoEmision = PuntoEmision::query()->create([
            'establecimiento_codigo' => '001',
            'punto_codigo' => '007',
        ]);
        $comprobante = Comprobante::query()->create([
            'tipo' => 'PREDIAL',
            'punto_emision_id' => $puntoEmision->id,
            'secuencial' => '000000001',
            'fecha_emision' => now()->toDateString(),
            'usuario_id' => $usuarioId,
            'valor_total' => 1.25,
        ]);

        $resultado = app(AnulacionService::class)->anular($comprobante, 'Cobro registrado por error', $usuarioId);

        $this->assertSame('X', $resultado->estado);
        $this->assertSame('Cobro registrado por error', $resultado->motivo_anulacion);
        $this->assertSame($usuarioId, $resultado->anulado_por);
        $this->assertNotNull($resultado->anulado_en);
    }

    public function test_rejects_an_empty_reason(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(AnulacionService::class)->anular(new Comprobante, '  ', 10);
    }

    public function test_rejects_an_already_cancelled_document(): void
    {
        $usuarioId = $this->crearUsuario()->id;
        $puntoEmision = PuntoEmision::query()->create([
            'establecimiento_codigo' => '001',
            'punto_codigo' => '007',
        ]);
        $comprobante = Comprobante::query()->create([
            'tipo' => 'PREDIAL',
            'punto_emision_id' => $puntoEmision->id,
            'secuencial' => '000000001',
            'fecha_emision' => now()->toDateString(),
            'usuario_id' => $usuarioId,
            'valor_total' => 1.25,
            'estado' => 'X',
        ]);

        $this->expectException(LogicException::class);

        app(AnulacionService::class)->anular($comprobante, 'Segundo intento', $usuarioId);
    }

    private function crearUsuario(): User
    {
        $perfil = Perfil::query()->create(['nombre' => 'ADMINISTRADOR']);

        return User::query()->create([
            'perfil_id' => $perfil->id,
            'nombres' => 'Administrador de pruebas',
            'correo' => 'admin@example.test',
            'password_hash' => 'password',
            'estado' => 'ACTIVO',
        ]);
    }
}
