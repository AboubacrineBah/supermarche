<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_dashboard_displays_tenant_statistics(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);

        Product::factory()->for($tenant)->create(['is_active' => true]);
        Product::factory()->for($tenant)->create(['is_active' => false]);

        $this->actingAs($user)
            ->get('/home')
            ->assertOk()
            ->assertViewHas('stats', function (array $stats): bool {
                return $stats['products'] === 2
                    && $stats['active_products'] === 1;
            });
    }
}
