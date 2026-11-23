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
        Schema::create('arqueo_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arqueo_id')->constrained('arqueo_caja')->cascadeOnDelete();
            $table->string('tipo', 10);
            $table->decimal('denominacion', 12, 2);
            $table->unsignedInteger('cantidad')->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arqueo_detalle');
    }
};
