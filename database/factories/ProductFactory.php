<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $costPrice = fake()->numberBetween(500, 20000);

        return [
            'tenant_id' => Tenant::factory(),
            'category_id' => null,
            'unit_id' => null,
            'sku' => 'P-'.fake()->unique()->numerify('######'),
            'barcode' => fake()->unique()->ean13(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'cost_price' => $costPrice,
            'retail_price' => (int) round($costPrice * 1.25),
            'wholesale_price' => null,
            'wholesale_min_qty' => null,
            'stock_min' => 5,
            'stock_max' => null,
            'track_lots' => false,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
