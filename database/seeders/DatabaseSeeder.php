<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SettingsSeeder::class,
            UsersWithRolesSeeder::class,
            CountrySeeder::class,
            CurrencySeeder::class,
            PolicySeeder::class,
            AboutUsSeeder::class,
            ContactUsSeeder::class,
            SupplierPageSeeder::class,
            ProductTypeSeeder::class,
        ]);

        $this->command->info('Data seeded successfully.');
    }
}

