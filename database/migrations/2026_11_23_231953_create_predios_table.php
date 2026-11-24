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
        Schema::create('predio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribuyente_id')->constrained('contribuyente')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('codigo_catastral')->unique();
            $table->string('matricula')->nullable();
            $table->date('fecha_escritura')->nullable();
            $table->date('fecha_inicio_contribucion')->nullable();
            $table->string('zona', 20);
            $table->text('ubicacion');
            $table->decimal('area_m2', 12, 2)->nullable();
            $table->decimal('avaluo_actual', 12, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->string('estado', 20)->default('ACTIVO');
            $table->index('contribuyente_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predio');
    }
};
