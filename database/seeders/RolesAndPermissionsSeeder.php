<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Rôles et permissions de base de SuguPro.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            'stock.adjust',
            'purchases.manage',
            'pos.sell',
            'pos.discount',
            'pos.cancel',
            'cash.open',
            'cash.close',
            'customers.manage',
            'credit.grant',
            'reports.view',
            'settings.manage',
            'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web'])
            ->syncPermissions($permissions);

        Role::firstOrCreate(['name' => 'gerant', 'guard_name' => 'web'])
            ->syncPermissions($permissions);

        Role::firstOrCreate(['name' => 'caissier', 'guard_name' => 'web'])
            ->syncPermissions([
                'products.view',
                'pos.sell',
                'pos.discount',
                'cash.open',
                'cash.close',
                'customers.manage',
            ]);

        Role::firstOrCreate(['name' => 'magasinier', 'guard_name' => 'web'])
            ->syncPermissions([
                'products.view',
                'products.create',
                'products.update',
                'stock.adjust',
                'purchases.manage',
            ]);

        Role::firstOrCreate(['name' => 'comptable', 'guard_name' => 'web'])
            ->syncPermissions([
                'products.view',
                'reports.view',
            ]);
    }
}
