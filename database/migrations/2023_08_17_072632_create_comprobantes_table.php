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
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_comprobante');
            $table->string('serie');
            $table->string('numero');
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('monto_total', 10, 2);
            $table->decimal('monto_subtotal', 10, 2);
            $table->decimal('monto_igv', 10, 2);
            $table->string('tipo_moneda');
            $table->unsignedBigInteger('cliente_id');
            $table->string('estado_pago');
            $table->string('adjunto')->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};
