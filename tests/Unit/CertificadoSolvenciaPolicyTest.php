<?php

namespace Tests\Unit;

use App\Models\CertificadoSolvencia;
use App\Models\Perfil;
use App\Models\User;
use App\Policies\CertificadoSolvenciaPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificadoSolvenciaPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrador_can_view_create_and_anular(): void
    {
        $usuario = $this->crearUsuario('ADMINISTRADOR');
        $politica = new CertificadoSolvenciaPolicy;
        $certificado = new CertificadoSolvencia;

        $this->assertTrue($politica->viewAny($usuario));
        $this->assertTrue($politica->view($usuario, $certificado));
        $this->assertTrue($politica->create($usuario));
        $this->assertTrue($politica->anular($usuario, $certificado));
        $this->assertFalse($politica->update($usuario, $certificado));
        $this->assertFalse($politica->delete($usuario, $certificado));
    }

    public function test_recaudador_can_view_and_create_but_not_anular(): void
    {
        $usuario = $this->crearUsuario('RECAUDADOR');
        $politica = new CertificadoSolvenciaPolicy;
        $certificado = new CertificadoSolvencia;

        $this->assertTrue($politica->viewAny($usuario));
        $this->assertTrue($politica->create($usuario));
        $this->assertFalse($politica->anular($usuario, $certificado));
    }

    public function test_contribuyente_cannot_access_the_module(): void
    {
        $usuario = $this->crearUsuario('CONTRIBUYENTE');
        $politica = new CertificadoSolvenciaPolicy;
        $certificado = new CertificadoSolvencia;

        $this->assertFalse($politica->viewAny($usuario));
        $this->assertFalse($politica->view($usuario, $certificado));
        $this->assertFalse($politica->create($usuario));
    }

    private function crearUsuario(string $perfilNombre): User
    {
        $perfil = Perfil::query()->create(['nombre' => $perfilNombre]);

        return User::query()->create([
            'perfil_id' => $perfil->id,
            'nombres' => "Usuario {$perfilNombre}",
            'correo' => strtolower($perfilNombre).'@example.test',
            'password_hash' => 'password',
            'estado' => 'ACTIVO',
        ]);
    }
}
