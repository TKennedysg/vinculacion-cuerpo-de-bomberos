<?php

namespace Database\Seeders;

use App\Models\Perfil;
use Illuminate\Database\Seeder;

class PerfilSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['nombre' => 'ADMINISTRADOR', 'descripcion' => 'Acceso total al sistema.'],
            ['nombre' => 'RECAUDADOR', 'descripcion' => 'Emisión y cobro de documentos.'],
            ['nombre' => 'CONTRIBUYENTE', 'descripcion' => 'Consulta de su información y obligaciones.'],
        ] as $perfil) {
            Perfil::query()->updateOrCreate(
                ['nombre' => $perfil['nombre']],
                ['descripcion' => $perfil['descripcion']],
            );
        }
    }
}
