<?php

namespace Database\Seeders;

use App\Models\League;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin (Admin Panel)
        $admin = User::firstOrCreate(
        ['email' => 'admin@bbsaas.com'],
        [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]
        );

        // Asignar rol super_admin (si existe via Shield o crearlo)
        // Spatie/Shield usually handles this, but let's ensure basic access
        // For now, Filament Shield might control access via panel policies

        // 2. Demo League (Tenant)
        $league = League::firstOrCreate(
        ['slug' => 'liga-demo'],
        [
            'name' => 'Liga Demo',
            'status' => 'active', // Assuming status column exists from migration
        ]
        );

        // 3. League Owner (App Panel)
        $owner = User::firstOrCreate(
        ['email' => 'owner@liga-demo.com'],
        [
            'name' => 'League Owner',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]
        );

        // Attach user to league
        if (!$owner->leagues()->where('league_id', $league->id)->exists()) {
            $owner->leagues()->attach($league);
        }
    }
}