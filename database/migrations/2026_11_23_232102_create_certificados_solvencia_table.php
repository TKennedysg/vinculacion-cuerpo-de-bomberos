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
        Schema::create('certificado_solvencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comprobante_id')->unique()->constrained('comprobante')->cascadeOnDelete();
            $table->foreignId('tipo_tramite_id')->constrained('tipo_tramite')->restrictOnDelete();
            $table->text('descripcion_tramite');
            $table->string('a_favor_de');
            $table->text('leyenda');
            $table->decimal('valor', 12, 2)->default(10.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificado_solvencia');
    }
};
