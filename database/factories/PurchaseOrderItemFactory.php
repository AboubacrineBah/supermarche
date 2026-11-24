<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrderItem>
 */
class PurchaseOrderItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->randomFloat(2, 1, 50);

        return [
            'tenant_id' => PurchaseOrder::query()->findOrFail(
                Product::factory()->create()->purchaseOrders()->first()?->getKey() ?? 1,
            )->tenant_id,
            'purchase_order_id' => PurchaseOrder::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_cost' => fake()->numberBetween(500, 20000),
            'received_quantity' => 0,
            'line_total' => 0,
        ];
    }
}
