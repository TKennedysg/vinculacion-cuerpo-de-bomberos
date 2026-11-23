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
        Schema::create('solvencia_predio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificado_id')->constrained('certificado_solvencia')->cascadeOnDelete();
            $table->string('codigo_catastral');
            $table->string('matricula')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solvencia_predio');
    }
};
