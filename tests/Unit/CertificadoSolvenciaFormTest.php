<?php

namespace Tests\Unit;

use App\Filament\Resources\CertificadoSolvencias\Schemas\CertificadoSolvenciaForm;
use App\Models\Contribuyente;
use App\Models\Predio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificadoSolvenciaFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_no_debt_when_the_real_service_and_mock_are_absent(): void
    {
        $this->skipIfRealDebtServiceExists();

        config(['solvencia.mock_deuda' => false]);

        $contribuyente = $this->crearContribuyente();

        $this->assertSame(
            ['adeuda' => false, 'anios' => []],
            CertificadoSolvenciaForm::deuda($contribuyente->id),
        );
    }

    public function test_reports_mock_debt_when_enabled(): void
    {
        $this->skipIfRealDebtServiceExists();

        config([
            'solvencia.mock_deuda' => true,
            'solvencia.mock_anios' => [2024, 2025],
        ]);

        $contribuyente = $this->crearContribuyente();

        $this->assertSame(
            ['adeuda' => true, 'anios' => [2024, 2025]],
            CertificadoSolvenciaForm::deuda($contribuyente->id),
        );
    }

    public function test_returns_no_debt_for_a_missing_contributor(): void
    {
        $this->assertSame(['adeuda' => false, 'anios' => []], CertificadoSolvenciaForm::deuda(null));
        $this->assertSame(['adeuda' => false, 'anios' => []], CertificadoSolvenciaForm::deuda(999999));
    }

    public function test_lists_only_certificable_predios(): void
    {
        $contribuyente = $this->crearContribuyente();
        $activo = $this->crearPredio($contribuyente, 'CAT-ACTIVO', 'ACTIVO');
        $inactivo = $this->crearPredio($contribuyente, 'CAT-INACTIVO', 'INACTIVO');
        $anulado = $this->crearPredio($contribuyente, 'CAT-ANULADO', 'ANULADO');
        $transferido = $this->crearPredio($contribuyente, 'CAT-TRANSFERIDO', 'TRANSFERIDO');

        $predios = CertificadoSolvenciaForm::prediosCertificables($contribuyente->id);

        $this->assertArrayHasKey($activo->id, $predios);
        $this->assertArrayHasKey($inactivo->id, $predios);
        $this->assertArrayNotHasKey($anulado->id, $predios);
        $this->assertArrayNotHasKey($transferido->id, $predios);
    }

    private function skipIfRealDebtServiceExists(): void
    {
        if (class_exists('App\\Services\\EstadoCuentaPredialService')) {
            $this->markTestSkipped('EstadoCuentaPredialService ya existe; probar la integración real.');
        }
    }

    private function crearContribuyente(): Contribuyente
    {
        return Contribuyente::query()->create([
            'pais_origen' => 'Ecuador',
            'tipo_contribuyente' => 'NATURAL',
            'tipo_identificacion' => 'CEDULA',
            'identificacion' => '1712345678',
            'razon_social' => 'Contribuyente de pruebas',
            'telefono' => '0999999999',
            'estado' => 'ACTIVO',
        ]);
    }

    private function crearPredio(Contribuyente $contribuyente, string $codigoCatastral, string $estado): Predio
    {
        return Predio::query()->create([
            'contribuyente_id' => $contribuyente->id,
            'codigo_catastral' => $codigoCatastral,
            'zona' => 'URBANA',
            'ubicacion' => 'Calle de pruebas',
            'avaluo_actual' => 0,
            'estado' => $estado,
        ]);
    }
}
