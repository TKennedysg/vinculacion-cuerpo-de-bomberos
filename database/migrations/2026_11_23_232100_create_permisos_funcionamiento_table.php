<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permiso_funcionamiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comprobante_id')->unique()->constrained('comprobante')->cascadeOnDelete();
            $table->foreignId('establecimiento_id')->constrained('establecimiento')->restrictOnDelete();
            $table->string('concepto');
            $table->unsignedSmallInteger('anio_desde');
            $table->unsignedSmallInteger('anio_hasta');
            $table->text('direccion_local');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permiso_funcionamiento');
    }
};
