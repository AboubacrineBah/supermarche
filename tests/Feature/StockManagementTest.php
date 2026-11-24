<?php

namespace Tests\Feature;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Tenant;
use App\Models\User;
use App\Services\StockService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StockManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_creating_a_product_creates_an_empty_stock_row(): void
    {
        $tenant = Tenant::factory()->create();
        $user = $this->userFor($tenant, 'gerant');

        $this->actingAs($user)
            ->post(route('products.store'), $this->validPayload())
            ->assertRedirect();

        $product = Product::firstOrFail();

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'tenant_id' => $tenant->id,
            'quantity' => 0,
        ]);
    }

    public function test_magasinier_can_adjust_stock(): void
    {
        $tenant = Tenant::factory()->create();
        $user = $this->userFor($tenant, 'magasinier');
        $product = Product::factory()->for($tenant)->create();

        $this->actingAs($user)
            ->post(route('stocks.adjust.store', $product), [
                'quantity' => '25',
                'reason' => 'Inventaire initial',
            ])
            ->assertRedirect(route('stocks.movements', $product));

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'quantity' => 25,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'adjustment',
            'quantity' => 25,
            'reason' => 'Inventaire initial',
        ]);
    }

    public function test_stock_adjustment_requires_a_reason(): void
    {
        $tenant = Tenant::factory()->create();
        $user = $this->userFor($tenant, 'magasinier');
        $product = Product::factory()->for($tenant)->create();

        $this->actingAs($user)
            ->post(route('stocks.adjust.store', $product), ['quantity' => '10'])
            ->assertSessionHasErrors('reason');

        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_stock_movement_cannot_make_stock_negative(): void
    {
        $tenant = Tenant::factory()->create();
        $user = $this->userFor($tenant, 'magasinier');
        $product = Product::factory()->for($tenant)->create();

        $service = app(StockService::class);
        $service->adjust($product, 5, $user, 'Stock initial');

        $this->expectException(ValidationException::class);

        $service->record($product, StockMovementType::Sale, 10, $user, 'Vente');
    }

    public function test_caissier_cannot_adjust_stock(): void
    {
        $tenant = Tenant::factory()->create();
        $user = $this->userFor($tenant, 'caissier');
        $product = Product::factory()->for($tenant)->create();

        $this->actingAs($user)->get(route('stocks.adjust', $product))->assertForbidden();

        $this->actingAs($user)
            ->post(route('stocks.adjust.store', $product), ['quantity' => '10', 'reason' => 'Test'])
            ->assertForbidden();
    }

    public function test_stock_index_filters_low_stock_products(): void
    {
        $tenant = Tenant::factory()->create();
        $user = $this->userFor($tenant, 'gerant');

        $lowProduct = Product::factory()->for($tenant)->create([
            'name' => 'Produit bas',
            'stock_min' => 5,
        ]);
        Stock::factory()->for($lowProduct)->create(['quantity' => 2]);

        $okProduct = Product::factory()->for($tenant)->create([
            'name' => 'Produit ok',
            'stock_min' => 5,
        ]);
        Stock::factory()->for($okProduct)->create(['quantity' => 20]);

        $this->actingAs($user)
            ->get(route('stocks.index', ['status' => 'low']))
            ->assertOk()
            ->assertSee('Produit bas')
            ->assertDontSee('Produit ok');
    }

    public function test_stock_pages_are_isolated_by_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $user = $this->userFor($tenantA, 'gerant');

        $foreignProduct = Product::factory()->for($tenantB)->create(['name' => 'Produit externe']);
        Stock::factory()->for($foreignProduct)->create(['quantity' => 10]);

        $this->actingAs($user)->get(route('stocks.adjust', $foreignProduct))->assertNotFound();
        $this->actingAs($user)->get(route('stocks.movements', $foreignProduct))->assertNotFound();

        $this->actingAs($user)
            ->get(route('stocks.index'))
            ->assertOk()
            ->assertDontSee('Produit externe');
    }

    public function test_movements_history_lists_the_adjustment(): void
    {
        $tenant = Tenant::factory()->create();
        $user = $this->userFor($tenant, 'magasinier');
        $product = Product::factory()->for($tenant)->create();

        app(StockService::class)->adjust($product, 12, $user, 'Inventaire');

        $this->actingAs($user)
            ->get(route('stocks.movements', $product))
            ->assertOk()
            ->assertSee('Ajustement')
            ->assertSee('Inventaire')
            ->assertSee('+12');
    }

    public function test_product_with_stock_movements_cannot_be_deleted(): void
    {
        $tenant = Tenant::factory()->create();
        $user = $this->userFor($tenant, 'gerant');
        $product = Product::factory()->for($tenant)->create();

        app(StockService::class)->record($product, StockMovementType::Opening, 10, $user, 'Stock initial');

        $this->actingAs($user)
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Riz parfumé 5kg',
            'sku' => 'RIZ-'.fake()->unique()->numerify('#####'),
            'barcode' => null,
            'category_id' => null,
            'unit_id' => null,
            'description' => null,
            'cost_price' => 4000,
            'retail_price' => 5000,
            'wholesale_price' => null,
            'wholesale_min_qty' => null,
            'stock_min' => 5,
            'stock_max' => null,
            'track_lots' => '1',
            'is_active' => '1',
        ], $overrides);
    }

    private function userFor(Tenant $tenant, string $role): User
    {
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $user->assignRole($role);

        return $user;
    }
}
