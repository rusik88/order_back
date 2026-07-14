<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Role::factory()->create(
            [
                'name'          => 'Super Admin',
                'slug'          => 'super_admin',
                'permissions'   => '["order:read", "order:create", "order:update", "order:delete", "order_status:read", "order_status:create", "order_status:update", "order_status:delete", "role:read", "role:create", "role:update", "role:delete", "user:read", "user:create", "user:update", "user:delete", "setting:read", "setting:update"]',
            ],
            [
                'name'          => 'Admin',
                'slug'          => 'admin',
                'permissions'   => '["order:read", "order:create", "order:update", "order:delete", "order_status:read", "order_status:create", "order_status:update", "order_status:delete", "role:read", "role:create", "role:update", "role:delete", "user:read", "user:create", "user:update", "user:delete"]',
            ],
            [
                'name'          => 'Manager',
                'slug'          => 'manager',
                'permissions'   => '["order:read", "order:create", "order:update", "order:delete", "order_status:read", "order_status:create", "order_status:update", "order_status:delete"]',
            ]
        );

        User::factory()->create([
            'name'      => 'WebDevelop',
            'email'     => 'web.develop54@gmail.com',
            'password'  => Hash::make('password'),
            'role_id'   => 1,
        ]);

        Setting::factory()->create(
            [
                'title' => 'Default Role',
                'key'   => 'default_role',
                'value' => '3',
                'type'  => 'role',
            ],
            [
                'title' => 'Lifetime Token',
                'key'   => 'lifetime_token',
                'value' => '1440',
                'type'  => 'number',
            ],
            [
                'title' => 'Name Project',
                'key'   => 'project_name',
                'value' => 'Order Crm',
                'type'  => 'string',
            ],
        );
    }
}
