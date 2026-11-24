<?php

namespace Tests\Unit;

use App\Filament\Resources\Users\Schemas\UserForm;
use App\Models\Perfil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_administrator_cannot_edit_own_profile_or_status(): void
    {
        $perfil = Perfil::query()->create(['nombre' => 'ADMINISTRADOR']);
        $usuario = User::query()->create([
            'perfil_id' => $perfil->id,
            'nombres' => 'Emilio',
            'correo' => 'emilio@example.test',
            'password_hash' => 'password',
            'estado' => 'ACTIVO',
        ]);

        $this->actingAs($usuario);

        $this->assertTrue(UserForm::shouldDisableSelfManagedFields('edit', $usuario));
        $this->assertFalse(UserForm::shouldDisableSelfManagedFields('create', null));
    }
}
