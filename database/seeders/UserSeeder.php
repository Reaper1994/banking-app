<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin']);
        }
        if (! Role::where('name', 'client')->exists()) {
            Role::create(['name' => 'client']);
        }

        foreach ([
            'view_dashboard',
            'manage_users',
            'manage_accounts',
            'manage_transactions',
            'view_accounts',
            'view_transactions',
        ] as $permission) {
            if (! Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        $adminRole = Role::where('name', 'admin')->first();
        $adminRole->givePermissionTo([
            'view_dashboard',
            'manage_users',
            'manage_accounts',
            'manage_transactions',
            'view_accounts',
            'view_transactions',
        ]);

        $clientRole = Role::where('name', 'client')->first();
        $clientRole->givePermissionTo([
            'view_dashboard',
            'view_transactions',
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'date_of_birth' => '1990-01-01',
                'address' => '123 Admin Street',
            ]
        );
        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }


        $clients = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
                'first_name' => 'John',
                'last_name' => 'Doe',
                'date_of_birth' => '1992-05-15',
                'address' => '456 Main Street',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => bcrypt('password'),
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'date_of_birth' => '1988-11-30',
                'address' => '789 Oak Avenue',
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'password' => bcrypt('password'),
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'date_of_birth' => '1995-03-20',
                'address' => '321 Pine Road',
            ],
        ];

        foreach ($clients as $clientData) {
            $client = User::firstOrCreate(
                ['email' => $clientData['email']],
                $clientData
            );
            if (! $client->hasRole('client')) {
                $client->assignRole('client');
            }
        }
    }
}
