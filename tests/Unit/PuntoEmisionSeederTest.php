<?php

namespace Tests\Unit;

use App\Models\PuntoEmision;
use Database\Seeders\PuntoEmisionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PuntoEmisionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_twice_does_not_reset_an_existing_sequence(): void
    {
        $seeder = new PuntoEmisionSeeder;

        $seeder->run();
        $punto = PuntoEmision::query()->sole();
        $punto->update(['secuencia_actual' => 27]);

        $seeder->run();

        $this->assertSame(27, PuntoEmision::query()->sole()->secuencia_actual);
    }
}
