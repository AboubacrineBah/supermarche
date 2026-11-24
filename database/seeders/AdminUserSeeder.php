<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    /**
     * Crée le compte administrateur initial.
     */
    public function run(): void
    {
        $password = config('admin.password');

        if (blank($password)) {
            if (app()->isProduction()) {
                throw new RuntimeException('Définissez ADMIN_PASSWORD avant de créer le compte administrateur.');
            }

            $password = Str::password(16);
            $this->command?->warn('Mot de passe administrateur généré : '.$password);
        }

        $user = User::firstOrCreate(
            ['email' => (string) config('admin.email')],
            [
                'name' => (string) config('admin.name'),
                'password' => $password,
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        );

        $user->syncRoles(['super-admin']);
    }
}
