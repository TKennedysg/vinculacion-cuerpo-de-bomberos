<?php

namespace Tests\Unit;

use App\Models\Perfil;
use App\Models\User;
use App\Policies\PerfilPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerfilPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_an_administrator_can_manage_profiles(): void
    {
        $administrador = $this->crearUsuario('ADMINISTRADOR');
        $recaudador = $this->crearUsuario('RECAUDADOR', 'recaudador@example.test');
        $policy = new PerfilPolicy;

        $this->assertTrue($policy->viewAny($administrador));
        $this->assertTrue($policy->create($administrador));
        $this->assertFalse($policy->viewAny($recaudador));
        $this->assertFalse($policy->create($recaudador));
    }

    private function crearUsuario(string $perfilNombre, string $correo = 'admin@example.test'): User
    {
        $perfil = Perfil::query()->create(['nombre' => $perfilNombre]);

        return User::query()->create([
            'perfil_id' => $perfil->id,
            'nombres' => $perfilNombre,
            'correo' => $correo,
            'password_hash' => 'password',
            'estado' => 'ACTIVO',
        ]);
    }
}
