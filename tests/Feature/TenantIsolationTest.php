<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_users_only_see_their_own_tenant_products(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        Product::factory()->for($tenantA)->create(['name' => 'Produit A']);
        Product::factory()->for($tenantB)->create(['name' => 'Produit B']);

        $user = User::factory()->create(['tenant_id' => $tenantA->id]);

        $this->actingAs($user)
            ->get('/home')
            ->assertOk()
            ->assertSee('Produit A')
            ->assertDontSee('Produit B');
    }

    public function test_super_admins_see_products_from_every_tenant(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        Product::factory()->for($tenantA)->create(['name' => 'Produit A']);
        Product::factory()->for($tenantB)->create(['name' => 'Produit B']);

        $admin = User::factory()->create(['tenant_id' => null]);
        $admin->assignRole('super-admin');

        $this->actingAs($admin)
            ->get('/home')
            ->assertOk()
            ->assertSee('Produit A')
            ->assertSee('Produit B');
    }

    public function test_users_without_a_tenant_are_forbidden(): void
    {
        $user = User::factory()->create(['tenant_id' => null]);

        $this->actingAs($user)->get('/home')->assertForbidden();
    }

    public function test_users_of_an_inactive_tenant_are_forbidden(): void
    {
        $tenant = Tenant::factory()->create(['is_active' => false]);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        $this->actingAs($user)->get('/home')->assertForbidden();
    }

    public function test_inactive_users_are_forbidden(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'is_active' => false,
        ]);

        $this->actingAs($user)->get('/home')->assertForbidden();
    }
}
