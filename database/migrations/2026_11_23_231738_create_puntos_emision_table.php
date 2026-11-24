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
        Schema::create('punto_emision', function (Blueprint $table) {
            $table->id();
            $table->string('establecimiento_codigo', 3);
            $table->string('punto_codigo', 3);
            $table->unsignedBigInteger('secuencia_actual')->default(0);
            $table->string('descripcion')->nullable();
            $table->unique(['establecimiento_codigo', 'punto_codigo']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punto_emision');
    }
};
