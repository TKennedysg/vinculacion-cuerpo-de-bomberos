<?php

namespace App\Console\Commands;

use App\Models\Perfil;
use App\Models\PuntoEmision;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

#[Signature('app:crear-usuario-admin {--nombres=} {--correo=} {--password=} {--punto-emision-id=}')]
#[Description('Crea el primer usuario administrador del sistema.')]
class CrearUsuarioAdmin extends Command
{
    public function handle(): int
    {
        $nombres = trim((string) $this->option('nombres'));
        $correo = trim((string) $this->option('correo'));
        $password = (string) $this->option('password');
        $puntoEmisionId = $this->option('punto-emision-id');

        $passwordIsStrong = strlen($password) >= 8
            && preg_match('/[a-z]/', $password) === 1
            && preg_match('/[A-Z]/', $password) === 1
            && preg_match('/\d/', $password) === 1
            && preg_match('/[^A-Za-z0-9]/', $password) === 1;

        if ($nombres === '' || strlen($nombres) > 255 || ! filter_var($correo, FILTER_VALIDATE_EMAIL) || ! $passwordIsStrong) {
            throw new InvalidArgumentException('Indica nombres válidos, un correo válido y una contraseña de 8 caracteres con mayúscula, minúscula, número y símbolo.');
        }

        if (User::query()->where('correo', $correo)->exists()) {
            $this->error('Ya existe un usuario con ese correo.');

            return self::FAILURE;
        }

        if ($puntoEmisionId !== null && ! PuntoEmision::query()->whereKey($puntoEmisionId)->exists()) {
            $this->error('El punto de emisión indicado no existe.');

            return self::FAILURE;
        }

        $usuario = DB::transaction(function () use ($nombres, $correo, $password, $puntoEmisionId): User {
            $perfil = Perfil::query()->firstOrCreate([
                'nombre' => 'ADMINISTRADOR',
            ], [
                'descripcion' => 'Acceso total al sistema.',
            ]);

            return User::query()->create([
                'perfil_id' => $perfil->id,
                'nombres' => $nombres,
                'correo' => $correo,
                'password_hash' => $password,
                'punto_emision_id' => $puntoEmisionId,
                'estado' => 'ACTIVO',
            ]);
        });

        $this->info("Administrador creado correctamente: {$usuario->correo}");

        return self::SUCCESS;
    }
}
