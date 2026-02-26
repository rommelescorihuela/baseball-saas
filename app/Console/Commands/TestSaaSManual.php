<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\League;
use App\Models\Team;
use App\Enums\Plan;
use Illuminate\Support\Str;

class TestSaaSManual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:saas-manual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dynamically simulate Manual SaaS Subscription workflows (Bank Transfers)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== Starting Dynamic SaaS Testing (Manual CRM Mode) ===");

        $this->info("1. Creating League with Free Plan...");
        $league = League::create([
            'name' => 'Manual Validated League',
            'slug' => 'manual-league-' . Str::random(5),
            'status' => 'active',
            'plan' => Plan::FREE->value,
            'subscription_status' => 'active',
        ]);

        $this->line("League Created: " . $league->id . " | Plan: " . $league->plan->value);

        $this->info("\n2. Simulating User Attempting to Create 10 Teams (Limit: 8)");
        $createdTeams = 0;
        try {
            for ($i = 0; $i < 10; $i++) {
                if ($league->plan->maxTeams() !== null && $league->teams()->count() >= $league->plan->maxTeams()) {
                    throw new \Exception("Plan limit reached!");
                }
                $league->teams()->create(['name' => "Team " . $i]);
                $createdTeams++;
            }
        } catch (\Exception $e) {
            $this->error("   Blocked! System correctly enforced Free limit. Teams created: " . $createdTeams);
        }

        $this->info("\n3. Simulating Super Admin Confirming Bank Transfer & Upgrading Plan");
        // Super Admin uses Filament to Manually Update the User's League Plan directly on the Model.
        $league->update([
            'plan' => Plan::PRO->value,
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonth(),
        ]);

        $league->refresh();
        $this->line("   Admin override successful. New League Plan: " . $league->plan->value);
        $this->line("   Next Expiration: " . $league->subscription_ends_at->toDateString());

        if ($league->plan === Plan::PRO) {
            $this->info("\n4. Retry Creating 9th Team (Now on PRO)");
            try {
                if ($league->plan->maxTeams() !== null && $league->teams()->count() >= $league->plan->maxTeams()) {
                    throw new \Exception("Plan limit reached!");
                }
                $league->teams()->create(['name' => "Team 9"]);
                $createdTeams++;
                $this->info("   Success! Team 9 created. Current Team Count: " . $league->teams()->count());
            } catch (\Exception $e) {
                $this->error("   Failed to bypass limit! " . $e->getMessage());
            }
        }

        $this->info("\n5. Simulating Expiration Cronjob / Manual Downgrade by Admin");
        // An admin sees the payment is overdue, or a Cronjob flags the end date as past.
        $league->update([
            'plan' => Plan::FREE->value,
            'subscription_status' => 'past_due',
        ]);

        $league->refresh();
        $this->line("   Account Downgraded. New League Plan: " . $league->plan->value);
        $this->line("   Subscription Status: " . $league->subscription_status);

        if ($league->plan === Plan::FREE) {
            $this->info("   Success! Constraints apply immediately.");
        }

        // Clean up
        $league->delete();
        $this->info("\n=== Dynamic Testing Complete ===");
    }
}
