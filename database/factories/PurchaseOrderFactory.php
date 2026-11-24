<?php

namespace Database\Factories;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'supplier_id' => Supplier::factory(),
            'user_id' => User::factory(),
            'reference' => 'BC-'.fake()->unique()->numerify('#####'),
            'status' => PurchaseOrderStatus::Draft,
            'ordered_at' => now(),
            'expected_at' => now()->addDays(7),
            'total' => 0,
            'notes' => null,
        ];
    }
}
