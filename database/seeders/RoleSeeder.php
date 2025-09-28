<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Enums\UserRole;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            UserRole::SuperAdmin,
            UserRole::Admin,
            UserRole::Client,
            UserRole::Blogger,
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->command->info('Roles seeded successfully.');
    }
}
