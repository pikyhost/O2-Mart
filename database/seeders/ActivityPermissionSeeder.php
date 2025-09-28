<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ActivityPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view_activity',
            'view_any_activity',
            'create_activity',
            'update_activity',
            'delete_activity',
            'delete_any_activity',
            'force_delete_activity',
            'force_delete_any_activity',
            'restore_activity',
            'restore_any_activity',
            'replicate_activity',
            'reorder_activity',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('✅ Activity permissions seeded successfully.');
    }
}
