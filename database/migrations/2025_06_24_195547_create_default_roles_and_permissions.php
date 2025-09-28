<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration {
    public function up(): void
    {
        $roles = ['super_admin', 'admin', 'client', 'blogger'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        $permissions = [
            // Users
            'view_user', 'view_any_user',
            'create_user', 'update_user', 'delete_user', 'delete_any_user',
            // Roles
            'view_role', 'view_any_role',
            'create_role', 'update_role', 'delete_role', 'delete_any_role',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // Assign all to super_admin
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        // Assign only view & update to admin
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $adminPermissions = Permission::where(function ($query) {
                $query->where('name', 'like', 'view%')
                      ->orWhere('name', 'like', 'update%');
            })->get();

            $admin->syncPermissions($adminPermissions);
        }
    }

    public function down(): void
    {
        Role::whereIn('name', ['super_admin', 'admin', 'client', 'blogger'])->delete();
        Permission::whereIn([
            'view_user', 'view_any_user',
            'create_user', 'update_user', 'delete_user', 'delete_any_user',
            'view_role', 'view_any_role',
            'create_role', 'update_role', 'delete_role', 'delete_any_role',
        ])->delete();
    }
};
