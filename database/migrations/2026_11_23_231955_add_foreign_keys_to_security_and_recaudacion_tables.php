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
        Schema::table('security_and_recaudacion_tables', function (Blueprint $table) {
            Schema::table('usuario', function (Blueprint $table): void {
                $table->foreign('contribuyente_id')->references('id')->on('contribuyente')->nullOnDelete();
            });

            Schema::table('comprobante', function (Blueprint $table): void {
                $table->foreign('punto_emision_id')->references('id')->on('punto_emision')->restrictOnDelete();
                $table->foreign('contribuyente_id')->references('id')->on('contribuyente')->nullOnDelete();
                $table->foreign('usuario_id')->references('id')->on('usuario')->restrictOnDelete();
                $table->foreign('anulado_por')->references('id')->on('usuario')->nullOnDelete();
            });

            Schema::table('predio_transferencia', function (Blueprint $table): void {
                $table->foreign('usuario_id')->references('id')->on('usuario')->restrictOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('security_and_recaudacion_tables', function (Blueprint $table) {
            Schema::table('comprobante', function (Blueprint $table): void {
                $table->dropForeign(['punto_emision_id']);
                $table->dropForeign(['contribuyente_id']);
                $table->dropForeign(['usuario_id']);
                $table->dropForeign(['anulado_por']);
            });

            Schema::table('predio_transferencia', function (Blueprint $table): void {
                $table->dropForeign(['usuario_id']);
            });

            Schema::table('usuario', function (Blueprint $table): void {
                $table->dropForeign(['contribuyente_id']);
            });
        });
    }
};
