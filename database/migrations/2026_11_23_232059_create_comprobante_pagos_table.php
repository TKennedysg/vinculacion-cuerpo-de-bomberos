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
        Schema::create('comprobante_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comprobante_id')->constrained('comprobante')->cascadeOnDelete();
            $table->string('forma_pago', 30);
            $table->decimal('valor', 12, 2);
            $table->string('numero_documento')->nullable();
            $table->string('banco')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprobante_pago');
    }
};
