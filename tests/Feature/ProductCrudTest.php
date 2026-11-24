<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_gerant_can_create_a_product_for_their_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $user = $this->userFor($tenant, 'gerant');
        $category = Category::factory()->create(['tenant_id' => $tenant->id]);
        $unit = Unit::factory()->create(['tenant_id' => $tenant->id]);

        $response = $this->actingAs($user)->post(route('products.store'), $this->validPayload([
            'sku' => 'RIZ-5KG',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
        ]));

        $product = Product::firstOrFail();

        $response->assertRedirect(route('products.show', $product));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'tenant_id' => $tenant->id,
            'sku' => 'RIZ-5KG',
            'name' => 'Riz parfumé 5kg',
        ]);
    }

    public function test_sku_must_be_unique_within_the_same_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $user = $this->userFor($tenant, 'gerant');

        Product::factory()->for($tenant)->create(['sku' => 'RIZ-5KG']);

        $this->actingAs($user)
            ->post(route('products.store'), $this->validPayload(['sku' => 'RIZ-5KG']))
            ->assertSessionHasErrors('sku');

        $this->assertDatabaseCount('products', 1);
    }

    public function test_category_must_belong_to_the_same_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $user = $this->userFor($tenantA, 'gerant');
        $foreignCategory = Category::factory()->create(['tenant_id' => $tenantB->id]);

        $this->actingAs($user)
            ->post(route('products.store'), $this->validPayload(['category_id' => $foreignCategory->id]))
            ->assertSessionHasErrors('category_id');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_super_admin_must_choose_a_tenant_when_creating_a_product(): void
    {
        $admin = User::factory()->create(['tenant_id' => null]);
        $admin->assignRole('super-admin');

        $this->actingAs($admin)
            ->post(route('products.store'), $this->validPayload())
            ->assertSessionHasErrors('tenant_id');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_super_admin_can_create_a_product_for_a_selected_tenant(): void
    {
        $admin = User::factory()->create(['tenant_id' => null]);
        $admin->assignRole('super-admin');
        $tenant = Tenant::factory()->create();

        $this->actingAs($admin)
            ->post(route('products.store'), $this->validPayload(['tenant_id' => $tenant->id]))
            ->assertRedirect();

        $this->assertDatabaseHas('products', ['tenant_id' => $tenant->id]);
    }

    public function test_product_listing_only_contains_the_current_tenant_products(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $user = $this->userFor($tenantA, 'gerant');

        Product::factory()->for($tenantA)->create(['name' => 'Produit Interne']);
        Product::factory()->for($tenantB)->create(['name' => 'Produit Externe']);

        $this->actingAs($user)
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee('Produit Interne')
            ->assertDontSee('Produit Externe');
    }

    public function test_a_product_from_another_tenant_is_not_found(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $user = $this->userFor($tenantA, 'gerant');
        $foreignProduct = Product::factory()->for($tenantB)->create();

        $this->actingAs($user)
            ->get(route('products.show', $foreignProduct))
            ->assertNotFound();
    }

    public function test_caissier_cannot_create_a_product(): void
    {
        $tenant = Tenant::factory()->create();
        $user = $this->userFor($tenant, 'caissier');

        $this->actingAs($user)->get(route('products.create'))->assertForbidden();

        $this->actingAs($user)
            ->post(route('products.store'), $this->validPayload())
            ->assertForbidden();
    }

    public function test_gerant_can_update_a_product_without_changing_its_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $otherTenant = Tenant::factory()->create();
        $user = $this->userFor($tenant, 'gerant');
        $product = Product::factory()->for($tenant)->create(['name' => 'Ancien nom']);

        $this->actingAs($user)
            ->put(route('products.update', $product), $this->validPayload([
                'tenant_id' => $otherTenant->id,
                'name' => 'Nouveau nom',
                'sku' => $product->sku,
            ]))
            ->assertRedirect(route('products.show', $product));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'tenant_id' => $tenant->id,
            'name' => 'Nouveau nom',
        ]);
    }

    public function test_gerant_can_delete_a_product(): void
    {
        $tenant = Tenant::factory()->create();
        $user = $this->userFor($tenant, 'gerant');
        $product = Product::factory()->for($tenant)->create();

        $this->actingAs($user)
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Riz parfumé 5kg',
            'sku' => 'RIZ-5KG-'.fake()->unique()->numerify('###'),
            'barcode' => null,
            'category_id' => null,
            'unit_id' => null,
            'description' => null,
            'cost_price' => 4000,
            'retail_price' => 5000,
            'wholesale_price' => null,
            'wholesale_min_qty' => null,
            'stock_min' => 10,
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
