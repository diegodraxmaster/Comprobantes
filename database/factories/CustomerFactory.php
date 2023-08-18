<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{

    public function definition(): array
    {
        return [
            'ruc' => $this->faker->unique()->numerify('###########'),
            'name' => $this->faker->company,
            'address' => $this->faker->address,
        ];
    }
}
