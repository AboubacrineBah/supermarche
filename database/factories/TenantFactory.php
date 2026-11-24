<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'phone' => '+223 70 '.fake()->numerify('## ## ##'),
            'address' => fake()->streetAddress().', Bamako',
            'currency' => 'XOF',
            'currency_symbol' => 'FCFA',
            'tax_rate' => 0,
            'is_active' => true,
        ];
    }
}
