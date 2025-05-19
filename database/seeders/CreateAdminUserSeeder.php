<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat atau ambil user admin
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'              => 'Administrator',
                'password'          => bcrypt('123456'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Buat atau ambil role Admin
        $role = Role::firstOrCreate(['name' => 'Admin']);

        // 3. Sync semua permission
        $allPermissionIds = Permission::pluck('id')->toArray();
        $role->syncPermissions($allPermissionIds);

        // 4. Assign role ke user
        $user->syncRoles([$role->name]);
    }
}
