<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stock>
 */
class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'tenant_id' => fn (array $attributes) => Product::query()
                ->findOrFail($attributes['product_id'])
                ->tenant_id,
            'quantity' => fake()->randomFloat(2, 0, 100),
        ];
    }
}
