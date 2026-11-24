<?php

namespace Database\Factories;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
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
            'user_id' => null,
            'type' => StockMovementType::Opening,
            'quantity' => fake()->randomFloat(2, 1, 50),
            'unit_cost' => null,
            'reason' => null,
            'occurred_at' => now(),
        ];
    }
}
