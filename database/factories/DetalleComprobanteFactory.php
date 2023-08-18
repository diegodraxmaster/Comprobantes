<?php

namespace Database\Factories;

use App\Models\DetalleComprobante;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetalleComprobante>
 */
class DetalleComprobanteFactory extends Factory
{

    public function definition(): array
    {
        $producto = Producto::inRandomOrder()->first();
        $cantidad = $this->faker->randomNumber(2);
        return [
            'id_comprobante' => 0, // Será asignado durante la creación del comprobante
            'id_producto' => $producto->id,
            'descripcion' => $this->faker->sentence,
            'cantidad' => $cantidad,
            'precio_unitario' => $producto->precio,
            'subtotal' => $producto->precio * $cantidad,
        ];
    }
}
