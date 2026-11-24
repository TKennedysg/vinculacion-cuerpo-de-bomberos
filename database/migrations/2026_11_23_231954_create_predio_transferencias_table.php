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
        Schema::create('predio_transferencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('predio_id')->constrained('predio')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('contribuyente_origen_id')->constrained('contribuyente')->restrictOnDelete();
            $table->foreignId('contribuyente_destino_id')->constrained('contribuyente')->restrictOnDelete();
            $table->date('fecha');
            $table->unsignedBigInteger('usuario_id');
            $table->text('motivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predio_transferencia');
    }
};
