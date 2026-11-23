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
        Schema::create('liquidacion_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comprobante_id')->constrained('comprobante')->cascadeOnDelete();
            $table->foreignId('predio_id')->constrained('predio')->restrictOnDelete();
            $table->unsignedSmallInteger('anio_fiscal');
            $table->decimal('avaluo', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('porcentaje_descuento', 5, 2)->default(0);
            $table->decimal('valor_descuento', 12, 2)->default(0);
            $table->decimal('total_base', 12, 2);
            $table->decimal('intereses', 12, 2)->default(0);
            $table->decimal('multa', 12, 2)->default(0);
            $table->decimal('gastos_administrativos', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->unique(['predio_id', 'anio_fiscal', 'comprobante_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liquidacion_detalle');
    }
};
