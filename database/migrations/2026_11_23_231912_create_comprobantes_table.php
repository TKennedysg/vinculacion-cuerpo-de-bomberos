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
        Schema::create('comprobante', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 20);
            $table->unsignedBigInteger('punto_emision_id');
            $table->string('secuencial', 9);
            $table->date('fecha_emision');
            $table->unsignedBigInteger('contribuyente_id')->nullable();
            $table->unsignedBigInteger('usuario_id');
            $table->decimal('valor_total', 12, 2);
            $table->text('observacion')->nullable();
            $table->string('estado', 1)->default('A');
            $table->text('motivo_anulacion')->nullable();
            $table->unsignedBigInteger('anulado_por')->nullable();
            $table->timestamp('anulado_en')->nullable();
            $table->unique(['punto_emision_id', 'secuencial']);
            $table->index('fecha_emision');
            $table->index('tipo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprobante');
    }
};
