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
        Schema::create('detalle_comprobante', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comprobante_id');
            $table->unsignedBigInteger('producto_servicio_id');
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('monto_total_detalle', 10, 2);
            $table->decimal('igv', 10, 2)->nullable();

            $table->timestamps();

            $table->foreign('comprobante_id')->references('id')->on('comprobantes');
            $table->foreign('producto_servicio_id')->references('id')->on('producto_servicio');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_comprobante');
    }
};
