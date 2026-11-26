<?php

namespace Database\Seeders;

use App\Models\TipoTramite;
use Illuminate\Database\Seeder;

/**
 * Catálogo del RF-34.
 *
 * TODO(Karpite): este seeder es de su módulo (datos de prueba). Se creó aquí
 * porque el catálogo es indispensable para emitir certificados de solvencia.
 */
class TipoTramiteSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Escritura',
            'Escritura-Hipoteca',
            'Fines Consiguientes',
            'Hipoteca',
            'Nombramiento',
            'Posesión Efectiva',
            'Rectificación de Medidas',
        ] as $nombre) {
            TipoTramite::query()->firstOrCreate([
                'nombre' => $nombre,
            ]);
        }
    }
}
