<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();

        \App\Models\Supplier::factory(10)->create();
        \App\Models\Customer::factory(10)->create();
        \App\Models\Producto::factory(20)->create();
        \App\Models\Comprobante::factory(50)->create()->each(function ($comprobante) {
            \App\Models\DetalleComprobante::factory(rand(1, 5))->create(['id_comprobante' => $comprobante->id]);
        });
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
