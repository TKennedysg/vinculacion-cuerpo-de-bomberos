<?php

namespace Database\Seeders;

use App\Models\PuntoEmision;
use Illuminate\Database\Seeder;

class PuntoEmisionSeeder extends Seeder
{
    public function run(): void
    {
        PuntoEmision::query()->firstOrCreate(
            [
                'establecimiento_codigo' => '001',
                'punto_codigo' => '001',
            ],
            [
                'secuencia_actual' => 0,
                'descripcion' => 'Punto principal de tesorería',
            ],
        );
    }
}
