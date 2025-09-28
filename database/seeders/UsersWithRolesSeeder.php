<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;

class UsersWithRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ahmed Labib',
                'email' => 'a.labeb@pikyhost.com',
                'password' => Hash::make('Piky@1234'),
                'role' => UserRole::SuperAdmin,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sabah',
                'email' => 'sabah@pikyhost.com',
                'password' => Hash::make('Piky@1234'),
                'role' => UserRole::SuperAdmin,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Mohamed Mustafa',
                'email' => 'mo.mostafa@pikyhost.com',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ahmed Yahia',
                'email' => 'ahmed.yahia.hakeem@gmail.com',
                'password' => Hash::make('Piky@1234'),
                'role' => UserRole::SuperAdmin,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'], // Ensure this is a plain string
                    'password' => $userData['password'],
                    'email_verified_at' => $userData['email_verified_at'],
                ]
            );


            // Assign role
            $role = Role::where('name', $userData['role']->value)->first();
            if ($role) {
                $user->assignRole($role);
            }
        }
        $this->command->info('Users with roles seeded successfully.');
    }
}
