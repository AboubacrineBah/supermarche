<?php

namespace Database\Seeders;

use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoTenantSeeder extends Seeder
{
    /**
     * Jeu de données de démonstration : un supermarché de Bamako.
     */
    public function run(): void
    {
        $tenant = Tenant::updateOrCreate(
            ['slug' => 'la-paix'],
            [
                'name' => 'Supermarché La Paix',
                'phone' => '+223 70 12 34 56',
                'address' => 'Hamdallaye ACI 2000, Bamako',
                'currency' => 'XOF',
                'currency_symbol' => 'FCFA',
                'tax_rate' => 0,
                'is_active' => true,
            ],
        );

        $units = collect([
            ['name' => 'Pièce', 'symbol' => 'pce', 'allow_decimal' => false],
            ['name' => 'Kilogramme', 'symbol' => 'kg', 'allow_decimal' => true],
            ['name' => 'Litre', 'symbol' => 'L', 'allow_decimal' => true],
            ['name' => 'Sac 25 kg', 'symbol' => 'sac', 'allow_decimal' => false],
            ['name' => 'Carton', 'symbol' => 'ctn', 'allow_decimal' => false],
        ])->mapWithKeys(function (array $unit) use ($tenant): array {
            $model = Unit::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $unit['name']],
                $unit,
            );

            return [$unit['name'] => $model];
        });

        $categories = collect([
            'Céréales & légumineuses',
            'Huiles & condiments',
            'Produits laitiers',
            'Boissons',
            'Hygiène & entretien',
            'Épicerie sucrée',
        ])->mapWithKeys(function (string $name, int $position) use ($tenant): array {
            $model = Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => Str::slug($name)],
                ['name' => $name, 'position' => $position],
            );

            return [$name => $model];
        });

        $catalog = [
            ['Riz parfumé Gambiaka 25 kg', 'Céréales & légumineuses', 'Sac 25 kg', 'RIZ-GAM-25', '6111234500017', 12500, 14500, 13800, 5, 10, false],
            ['Riz brisé local 25 kg', 'Céréales & légumineuses', 'Sac 25 kg', 'RIZ-BRI-25', '6111234500024', 9500, 11000, 10500, 5, 10, false],
            ['Sucre en poudre 1 kg', 'Épicerie sucrée', 'Kilogramme', 'SUC-1KG', '6111234500031', 550, 700, null, null, 20, false],
            ['Sucre 50 kg', 'Épicerie sucrée', 'Sac 25 kg', 'SUC-50KG', '6111234500048', 24000, 27500, 26000, 2, 3, false],
            ['Huile Dinor 5 L', 'Huiles & condiments', 'Pièce', 'HUI-DIN-5L', '6111234500055', 6000, 7000, 6700, 6, 12, false],
            ['Huile de palme 1 L', 'Huiles & condiments', 'Litre', 'HUI-PAL-1L', '6111234500062', 900, 1100, null, null, 12, false],
            ['Lait Bonnet Rouge 500 g', 'Produits laitiers', 'Pièce', 'LAI-BON-500', '6111234500079', 2200, 2600, 2450, 12, 24, true],
            ['Lait en poudre Vitalait 400 g', 'Produits laitiers', 'Pièce', 'LAI-VIT-400', '6111234500086', 2500, 3000, null, null, 12, true],
            ['Yaourt Fanico 500 ml', 'Produits laitiers', 'Pièce', 'YAO-FAN-500', '6111234500093', 700, 900, null, null, 10, true],
            ['Coca-Cola 1,5 L', 'Boissons', 'Pièce', 'BOI-COC-15', '6111234500109', 700, 900, 850, 6, 24, false],
            ['Eau minérale Aïcha 1,5 L', 'Boissons', 'Pièce', 'BOI-AIC-15', '6111234500116', 200, 300, 250, 12, 48, false],
            ['Jus Fruivio 1 L', 'Boissons', 'Pièce', 'BOI-FRU-1L', '6111234500123', 900, 1200, null, null, 12, false],
            ['Savon Le Sénou 400 g', 'Hygiène & entretien', 'Pièce', 'HYG-SEN-400', '6111234500130', 350, 450, 400, 12, 24, false],
            ['Détergent Omo 900 g', 'Hygiène & entretien', 'Pièce', 'HYG-OMO-900', '6111234500147', 1400, 1750, null, null, 10, false],
            ['Eau de javel 1 L', 'Hygiène & entretien', 'Litre', 'HYG-JAV-1L', '6111234500154', 400, 550, null, null, 12, false],
            ['Café Touba 250 g', 'Épicerie sucrée', 'Pièce', 'EPI-CAF-250', '6111234500161', 900, 1200, null, null, 8, false],
            ['Thé Lipton 25 sachets', 'Épicerie sucrée', 'Pièce', 'EPI-THE-25', '6111234500178', 700, 950, null, null, 8, false],
            ['Pâtes spaghetti 500 g', 'Céréales & légumineuses', 'Pièce', 'PAT-SPA-500', '6111234500185', 350, 500, null, null, 20, false],
            ['Tomate concentrée 400 g', 'Huiles & condiments', 'Pièce', 'CON-TOM-400', '6111234500192', 450, 650, null, null, 15, false],
            ['Sardines 125 g', 'Huiles & condiments', 'Pièce', 'CON-SAR-125', '6111234500208', 400, 600, null, null, 15, false],
            ['Farine de blé 1 kg', 'Céréales & légumineuses', 'Kilogramme', 'FAR-BLE-1KG', '6111234500215', 500, 700, null, null, 15, false],
            ['Œufs plateau de 30', 'Produits laitiers', 'Pièce', 'OEU-PLA-30', '6111234500222', 2500, 3000, null, null, 5, true],
            ['Banane (kg)', 'Céréales & légumineuses', 'Kilogramme', 'FRA-BAN-KG', '6111234500239', 600, 900, null, null, 5, false],
            ['Poulet congelé (kg)', 'Produits laitiers', 'Kilogramme', 'VOL-POU-KG', '6111234500246', 2200, 2800, null, null, 5, true],
        ];

        foreach ($catalog as [$name, $category, $unit, $sku, $barcode, $cost, $retail, $wholesale, $wholesaleMin, $stockMin, $trackLots]) {
            Product::updateOrCreate(
                ['tenant_id' => $tenant->id, 'sku' => $sku],
                [
                    'category_id' => $categories[$category]->id,
                    'unit_id' => $units[$unit]->id,
                    'barcode' => $barcode,
                    'name' => $name,
                    'cost_price' => $cost,
                    'retail_price' => $retail,
                    'wholesale_price' => $wholesale,
                    'wholesale_min_qty' => $wholesaleMin,
                    'stock_min' => $stockMin,
                    'track_lots' => $trackLots,
                    'is_active' => true,
                ],
            );
        }

        $demoQuantities = [36, 8, 0, 12, 24, 6, 18, 0, 9, 48, 72, 24, 36, 12, 4, 18, 30, 40, 25, 20, 15, 12, 6, 9];

        Product::query()
            ->where('tenant_id', $tenant->id)
            ->orderBy('id')
            ->get()
            ->each(function (Product $product, int $index) use ($tenant, $demoQuantities): void {
                if ($product->stockMovements()->exists()) {
                    return;
                }

                $quantity = $demoQuantities[$index] ?? 10;

                $stock = Stock::query()->updateOrCreate(
                    ['product_id' => $product->id],
                    ['tenant_id' => $tenant->id, 'quantity' => $quantity],
                );

                StockMovement::query()->create([
                    'tenant_id' => $tenant->id,
                    'product_id' => $product->id,
                    'user_id' => null,
                    'type' => StockMovementType::Opening,
                    'quantity' => $stock->quantity,
                    'reason' => 'Stock initial de démonstration',
                    'occurred_at' => now()->subDays(7),
                ]);
            });

        $this->createUser($tenant, 'Awa Traoré', 'gerant@lapaix.test', 'gerant');
        $this->createUser($tenant, 'Moussa Keïta', 'caissier@lapaix.test', 'caissier');
        $this->createUser($tenant, 'Fatoumata Diallo', 'magasinier@lapaix.test', 'magasinier');
    }

    private function createUser(Tenant $tenant, string $name, string $email, string $role): void
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'tenant_id' => $tenant->id,
                'name' => $name,
                'password' => bcrypt('MotDePasse!2026'),
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles([$role]);
    }
}
