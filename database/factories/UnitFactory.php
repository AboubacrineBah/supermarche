<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => 'Pièce',
            'symbol' => 'pce',
            'allow_decimal' => false,
        ];
    }

    public function kilogram(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Kilogramme',
            'symbol' => 'kg',
            'allow_decimal' => true,
        ]);
    }
}
