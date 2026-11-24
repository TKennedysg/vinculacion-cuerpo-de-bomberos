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
        Schema::create('contribuyente', function (Blueprint $table) {
            $table->id();
            $table->string('pais_origen');
            $table->string('tipo_contribuyente', 20);
            $table->string('tipo_identificacion', 20);
            $table->string('identificacion')->unique();
            $table->string('razon_social');
            $table->string('representante_legal')->nullable();
            $table->text('direccion')->nullable();
            $table->string('zona', 20)->nullable();
            $table->string('correo')->nullable();
            $table->string('telefono_domicilio')->nullable();
            $table->string('celular')->nullable();
            $table->string('telefono');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('estado', 20)->default('ACTIVO');
            $table->index('razon_social');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contribuyente');
    }
};
