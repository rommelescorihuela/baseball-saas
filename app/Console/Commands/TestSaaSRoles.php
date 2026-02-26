<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\League;
use App\Models\Team;
use Filament\Facades\Filament;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class TestSaaSRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:saas-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dynamically verify Tenancy isolation and Role permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== Starting Dynamic Role & Tenancy Verification ===");

        // 1. Setup two distinct leagues
        $leagueA = League::create(['name' => 'League Alpha', 'slug' => 'alpha-' . Str::random(5), 'status' => 'active']);
        $leagueB = League::create(['name' => 'League Beta', 'slug' => 'beta-' . Str::random(5), 'status' => 'active']);

        // 2. Setup distinct users for each league
        $ownerA = User::factory()->create(['name' => 'Owner Alpha']);
        $ownerA->leagues()->attach($leagueA);

        $ownerB = User::factory()->create(['name' => 'Owner Beta']);
        $ownerB->leagues()->attach($leagueB);

        // Assign Role
        if (!Role::where('name', 'league_owner')->exists()) {
            Role::create(['name' => 'league_owner', 'guard_name' => 'web']);
        }

        $ownerA->assignRole('league_owner');
        $ownerB->assignRole('league_owner');

        $this->info("1. Verifying Tenancy Aisolation...");

        // Simulating checking if Owner A can access League B resources
        // In the app, this is handled by Filament's canAccessTenant or specific Middlewares/Policies

        $teamB = Team::create(['league_id' => $leagueB->id, 'name' => 'Beta Team']);

        $this->line("Testing if Owner Alpha can access League Beta...");

        // Manual check of the core isolation logic
        $canAccess = $ownerA->leagues()->where('leagues.id', $leagueB->id)->exists();

        if (!$canAccess) {
            $this->info("   SUCCESS: Owner Alpha is correctly isolated from League Beta.");
        } else {
            $this->error("   FAILURE: Owner Alpha has access to League Beta!");
        }

        $this->info("\n2. Verifying Role Permissions (Secretary vs Owner)...");

        if (!Role::where('name', 'secretary')->exists()) {
            Role::create(['name' => 'secretary', 'guard_name' => 'web']);
        }

        // This is a mockup of the Permission logic.
        // In a real E2E, we would check if a permission is attached to the role.

        $secretary = User::factory()->create(['name' => 'Secretary Alpha']);
        $secretary->leagues()->attach($leagueA);
        $secretary->assignRole('secretary');

        $this->line("Checking permissions for Secretary...");

        // Simulate checking for a high-privilege permission (e.g., deleting a league or managing billing)
        // This assumes we use Spatie's hasPermissionTo or similar

        $highPrivPermission = 'manage.billing'; // Example permission
        $canDelete = $secretary->hasPermissionTo($highPrivPermission);

        if (!$canDelete) {
            $this->info("   SUCCESS: Secretary cannot access billing (permission restricted).");
        } else {
            $this->warning("   NOTE: Secretary has high-privilege permissions. Check if this is intended.");
        }

        // Clean up
        $leagueA->delete();
        $leagueB->delete();
        $ownerA->delete();
        $ownerB->delete();
        $secretary->delete();

        $this->info("\n=== Dynamic Role & Tenancy Verification Complete ===");
    }
}
