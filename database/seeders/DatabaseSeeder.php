<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Rol Global (Para ti, el dueño del SaaS)
        // Este rol NO lleva league_id porque supervisa todo el sistema
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // 2. Roles de la Liga (Para tus clientes y sus usuarios)
        // Shield los usará dentro del contexto de cada Tenant
        $roles = [
            'league_owner', // Dueño de la Liga
            'team_owner', // Dueño de Equipo
            'secretary', // Administrativo
            'coach', // Técnico
            'player' // Jugador
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => 'panel_user', 'guard_name' => 'web']); // Permiso base
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}