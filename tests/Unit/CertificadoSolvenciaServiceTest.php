<?php

namespace Tests\Unit;

use App\Models\Contribuyente;
use App\Models\Perfil;
use App\Models\Predio;
use App\Models\PuntoEmision;
use App\Models\TipoTramite;
use App\Models\User;
use App\Services\CertificadoSolvenciaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class CertificadoSolvenciaServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_emits_a_certificate_with_the_fixed_value_and_snapshot_of_predios(): void
    {
        $contexto = $this->crearContexto();

        $certificado = app(CertificadoSolvenciaService::class)->emitir([
            'contribuyente_id' => $contexto['contribuyente']->id,
            'tipo_tramite_id' => $contexto['tipoTramite']->id,
            'descripcion_tramite' => 'Trámite de escritura del inmueble',
            'a_favor_de' => 'Banco del Pacífico',
            'predio_ids' => [$contexto['predio']->id],
            'punto_emision_id' => $contexto['puntoEmision']->id,
            'usuario_id' => $contexto['usuario']->id,
        ], [
            ['forma_pago' => 'EFECTIVO', 'valor' => '10.00'],
        ]);

        $this->assertEquals(10.00, (float) $certificado->valor);
        $this->assertSame('SOLVENCIA', $certificado->comprobante->tipo);
        $this->assertEquals(10.00, (float) $certificado->comprobante->valor_total);
        $this->assertSame('000000001', $certificado->comprobante->secuencial);
        $this->assertSame(
            'La persona interesada podrá hacer uso de la presente Certificación únicamente para tramitación de: Escritura',
            $certificado->leyenda,
        );
        $this->assertCount(1, $certificado->predios);
        $this->assertSame('CAT-001', $certificado->predios->first()->codigo_catastral);
        $this->assertSame('MAT-001', $certificado->predios->first()->matricula);
        $this->assertSame(1, $contexto['puntoEmision']->fresh()->secuencia_actual);

        $this->assertDatabaseHas('comprobante_pago', [
            'comprobante_id' => $certificado->comprobante->id,
            'forma_pago' => 'EFECTIVO',
            'valor' => 10.00,
        ]);
    }

    public function test_stores_the_mora_observation_when_informed(): void
    {
        $contexto = $this->crearContexto();

        $certificado = app(CertificadoSolvenciaService::class)->emitir([
            'contribuyente_id' => $contexto['contribuyente']->id,
            'tipo_tramite_id' => $contexto['tipoTramite']->id,
            'descripcion_tramite' => 'Trámite de hipoteca',
            'a_favor_de' => 'Cooperativa de ahorro',
            'predio_ids' => [$contexto['predio']->id],
            'punto_emision_id' => $contexto['puntoEmision']->id,
            'usuario_id' => $contexto['usuario']->id,
            'observacion' => 'Se informó al contribuyente de la deuda predial antes de emitir.',
        ], [
            ['forma_pago' => 'EFECTIVO', 'valor' => '10.00'],
        ]);

        $this->assertSame(
            'Se informó al contribuyente de la deuda predial antes de emitir.',
            $certificado->comprobante->observacion,
        );
    }

    public function test_rejects_a_predio_that_does_not_belong_to_the_contribuyente(): void
    {
        $contexto = $this->crearContexto();
        $otroContribuyente = $this->crearContribuyente('1712345679', 'Otro contribuyente');
        $predioAjeno = $this->crearPredio($otroContribuyente, 'CAT-999');

        $this->expectException(InvalidArgumentException::class);

        app(CertificadoSolvenciaService::class)->emitir([
            'contribuyente_id' => $contexto['contribuyente']->id,
            'tipo_tramite_id' => $contexto['tipoTramite']->id,
            'descripcion_tramite' => 'Trámite de escritura',
            'a_favor_de' => 'Beneficiario',
            'predio_ids' => [$predioAjeno->id],
            'punto_emision_id' => $contexto['puntoEmision']->id,
            'usuario_id' => $contexto['usuario']->id,
        ], [
            ['forma_pago' => 'EFECTIVO', 'valor' => '10.00'],
        ]);
    }

    public function test_rejects_a_predio_that_is_not_certificable(): void
    {
        $contexto = $this->crearContexto();
        $predioAnulado = $this->crearPredio($contexto['contribuyente'], 'CAT-ANULADO');
        $predioAnulado->update(['estado' => 'ANULADO']);

        $this->expectException(InvalidArgumentException::class);

        app(CertificadoSolvenciaService::class)->emitir([
            'contribuyente_id' => $contexto['contribuyente']->id,
            'tipo_tramite_id' => $contexto['tipoTramite']->id,
            'descripcion_tramite' => 'Trámite de escritura',
            'a_favor_de' => 'Beneficiario',
            'predio_ids' => [$predioAnulado->id],
            'punto_emision_id' => $contexto['puntoEmision']->id,
            'usuario_id' => $contexto['usuario']->id,
        ], [
            ['forma_pago' => 'EFECTIVO', 'valor' => '10.00'],
        ]);
    }

    public function test_rejects_an_empty_predio_selection(): void
    {
        $contexto = $this->crearContexto();

        $this->expectException(InvalidArgumentException::class);

        app(CertificadoSolvenciaService::class)->emitir([
            'contribuyente_id' => $contexto['contribuyente']->id,
            'tipo_tramite_id' => $contexto['tipoTramite']->id,
            'descripcion_tramite' => 'Trámite de escritura',
            'a_favor_de' => 'Beneficiario',
            'predio_ids' => [],
            'punto_emision_id' => $contexto['puntoEmision']->id,
            'usuario_id' => $contexto['usuario']->id,
        ], [
            ['forma_pago' => 'EFECTIVO', 'valor' => '10.00'],
        ]);
    }

    public function test_rejects_a_payment_sum_that_does_not_match_the_certificate_value(): void
    {
        $contexto = $this->crearContexto();

        $this->expectException(InvalidArgumentException::class);

        app(CertificadoSolvenciaService::class)->emitir([
            'contribuyente_id' => $contexto['contribuyente']->id,
            'tipo_tramite_id' => $contexto['tipoTramite']->id,
            'descripcion_tramite' => 'Trámite de escritura',
            'a_favor_de' => 'Beneficiario',
            'predio_ids' => [$contexto['predio']->id],
            'punto_emision_id' => $contexto['puntoEmision']->id,
            'usuario_id' => $contexto['usuario']->id,
        ], [
            ['forma_pago' => 'EFECTIVO', 'valor' => '9.99'],
        ]);
    }

    public function test_pdf_view_renders_the_dynamic_leyenda(): void
    {
        $contexto = $this->crearContexto();

        $certificado = app(CertificadoSolvenciaService::class)->emitir([
            'contribuyente_id' => $contexto['contribuyente']->id,
            'tipo_tramite_id' => $contexto['tipoTramite']->id,
            'descripcion_tramite' => 'Trámite de escritura',
            'a_favor_de' => 'Beneficiario',
            'predio_ids' => [$contexto['predio']->id],
            'punto_emision_id' => $contexto['puntoEmision']->id,
            'usuario_id' => $contexto['usuario']->id,
        ], [
            ['forma_pago' => 'EFECTIVO', 'valor' => '10.00'],
        ]);

        $html = view('pdf.certificado-solvencia', ['certificado' => $certificado])->render();

        $this->assertStringContainsString('CERTIFICADO DE SOLVENCIA', $html);
        $this->assertStringContainsString('CAT-001', $html);
        $this->assertStringContainsString('Escritura', $html);
    }

    /**
     * @return array{usuario: User, puntoEmision: PuntoEmision, contribuyente: Contribuyente, predio: Predio, tipoTramite: TipoTramite}
     */
    private function crearContexto(): array
    {
        $perfil = Perfil::query()->create(['nombre' => 'ADMINISTRADOR']);

        $usuario = User::query()->create([
            'perfil_id' => $perfil->id,
            'nombres' => 'Recaudador de pruebas',
            'correo' => 'recaudador@example.test',
            'password_hash' => 'password',
            'estado' => 'ACTIVO',
        ]);

        $puntoEmision = PuntoEmision::query()->create([
            'establecimiento_codigo' => '001',
            'punto_codigo' => '007',
            'secuencia_actual' => 0,
        ]);

        $contribuyente = $this->crearContribuyente('1712345678', 'Contribuyente de pruebas');
        $predio = $this->crearPredio($contribuyente, 'CAT-001', 'MAT-001');

        $tipoTramite = TipoTramite::query()->create(['nombre' => 'Escritura']);

        return compact('usuario', 'puntoEmision', 'contribuyente', 'predio', 'tipoTramite');
    }

    private function crearContribuyente(string $identificacion, string $razonSocial): Contribuyente
    {
        return Contribuyente::query()->create([
            'pais_origen' => 'Ecuador',
            'tipo_contribuyente' => 'NATURAL',
            'tipo_identificacion' => 'CEDULA',
            'identificacion' => $identificacion,
            'razon_social' => $razonSocial,
            'telefono' => '0999999999',
            'estado' => 'ACTIVO',
        ]);
    }

    private function crearPredio(Contribuyente $contribuyente, string $codigoCatastral, ?string $matricula = null): Predio
    {
        return Predio::query()->create([
            'contribuyente_id' => $contribuyente->id,
            'codigo_catastral' => $codigoCatastral,
            'matricula' => $matricula,
            'zona' => 'URBANA',
            'ubicacion' => 'Calle de pruebas',
            'avaluo_actual' => 0,
            'estado' => 'ACTIVO',
        ]);
    }
}
