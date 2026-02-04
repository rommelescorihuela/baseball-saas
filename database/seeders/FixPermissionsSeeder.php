<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class FixPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure both role variants exist to cover all bases
        $r1 = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $r2 = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // 2. Find the admin user
        $admin = User::firstOrCreate(
        ['email' => 'admin@test.com'],
        [
            'name' => 'Super Admin',
            'password' => Hash::make('admin123'),
        ]
        );

        // 3. Assign BOTH roles
        $admin->assignRole($r1);
        $admin->assignRole($r2);

        $this->command->info("Assigned roles 'super-admin' and 'super_admin' to user {$admin->email}.");

        // 4. Reset password just in case
        $admin->password = Hash::make('admin123');
        $admin->save();
        $this->command->info("Password reset to 'admin123'.");

        // 5. Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->command->info("Permission cache cleared.");
    }
}