<?php

namespace Tests\Unit;

use App\Models\Perfil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_users_can_authenticate_with_correo(): void
    {
        $perfil = Perfil::query()->create(['nombre' => 'ADMINISTRADOR']);
        User::query()->create([
            'perfil_id' => $perfil->id,
            'nombres' => 'Emilio',
            'correo' => 'emilio@example.test',
            'password_hash' => 'password-seguro',
            'estado' => 'ACTIVO',
        ]);

        $this->assertTrue(Auth::attempt([
            'correo' => 'emilio@example.test',
            'password' => 'password-seguro',
        ]));
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        $perfil = Perfil::query()->create(['nombre' => 'ADMINISTRADOR']);
        User::query()->create([
            'perfil_id' => $perfil->id,
            'nombres' => 'Emilio',
            'correo' => 'inactivo@example.test',
            'password_hash' => 'password-seguro',
            'estado' => 'INACTIVO',
        ]);

        $this->assertFalse(Auth::attempt([
            'correo' => 'inactivo@example.test',
            'password' => 'password-seguro',
        ]));
    }
}
