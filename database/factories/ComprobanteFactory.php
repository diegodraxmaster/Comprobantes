<?php

namespace Database\Factories;

use App\Models\Comprobante;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comprobante>
 */
class ComprobanteFactory extends Factory
{
    public function definition(): array
    {
        $supplier = Supplier::inRandomOrder()->first();
        $customer = Customer::inRandomOrder()->first();

        return [
            'tipo_comprobante' => $this->faker->randomElement(['factura', 'boleta']),
            'numero_comprobante' => $this->faker->unique()->numerify('######'),
            'fecha_emision' => $this->faker->date(),
            'monto_total' => $this->faker->randomFloat(2, 100, 1000),
            'supplier_id' => $supplier->id,
            'customer_id' => $customer->id,
        ];
    }
}
