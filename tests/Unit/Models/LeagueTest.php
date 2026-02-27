<?php

namespace Tests\Unit\Models;

use App\Enums\Plan;
use App\Models\Category;
use App\Models\Competition;
use App\Models\League;
use App\Models\Season;
use App\Models\Team;

describe('League Model', function () {

    test('can create a league with factory', function () {
        $league = League::factory()->create();

        expect($league)->toBeInstanceOf(League::class)
            ->and($league->exists())->toBeTrue();
    });

    test('uses slug as route key name', function () {
        $league = new League;

        expect($league->getRouteKeyName())->toBe('slug');
    });

    test('casts plan as Plan enum', function () {
        $league = League::factory()->create(['plan' => 'free']);

        expect($league->plan)->toBeInstanceOf(Plan::class);
    });

    test('casts trial_ends_at as datetime', function () {
        $league = League::factory()->create(['trial_ends_at' => now()->addDays(7)]);

        expect($league->trial_ends_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    test('casts subscription_ends_at as datetime', function () {
        $league = League::factory()->create(['subscription_ends_at' => now()->addMonth()]);

        expect($league->subscription_ends_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    test('has many teams', function () {
        $league = League::factory()->create();
        Team::factory()->count(3)->create(['league_id' => $league->id]);

        expect($league->teams)->toHaveCount(3)
            ->and($league->teams->first())->toBeInstanceOf(Team::class);
    });

    test('has many seasons', function () {
        $league = League::factory()->create();
        Season::factory()->count(2)->create(['league_id' => $league->id]);

        expect($league->seasons)->toHaveCount(2)
            ->and($league->seasons->first())->toBeInstanceOf(Season::class);
    });

    test('belongs to many users', function () {
        $league = League::factory()->create();
        $user = \App\Models\User::factory()->create();
        $league->users()->attach($user);

        expect($league->users)->toHaveCount(1)
            ->and($league->users->first())->toBeInstanceOf(\App\Models\User::class);
    });
});

describe('League isSubscribed Method', function () {

    test('returns true for free plan', function () {
        $league = League::factory()->create(['plan' => Plan::FREE]);

        expect($league->isSubscribed())->toBeTrue();
    });

    test('returns true when subscription_status is active', function () {
        $league = League::factory()->create([
            'plan' => Plan::PRO,
            'subscription_status' => 'active',
        ]);

        expect($league->isSubscribed())->toBeTrue();
    });

    test('returns true when subscription_status is trialing', function () {
        $league = League::factory()->create([
            'plan' => Plan::PRO,
            'subscription_status' => 'trialing',
        ]);

        expect($league->isSubscribed())->toBeTrue();
    });

    test('returns true when subscription_ends_at is in the future', function () {
        $league = League::factory()->create([
            'plan' => Plan::PRO,
            'subscription_status' => 'canceled',
            'subscription_ends_at' => now()->addDays(5),
        ]);

        expect($league->isSubscribed())->toBeTrue();
    });

    test('returns false when subscription has ended', function () {
        $league = League::factory()->create([
            'plan' => Plan::PRO,
            'subscription_status' => 'canceled',
            'subscription_ends_at' => now()->subDays(5),
        ]);

        expect($league->isSubscribed())->toBeFalse();
    });

    test('returns false when no active subscription for pro plan', function () {
        $league = League::factory()->create([
            'plan' => Plan::PRO,
            'subscription_status' => 'canceled',
            'subscription_ends_at' => null,
        ]);

        expect($league->isSubscribed())->toBeFalse();
    });
});

describe('League canCreateTeam Method', function () {

    test('returns true for free plan when under limit', function () {
        $league = League::factory()->create(['plan' => Plan::FREE]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $teams = Team::factory()->count(5)->create(['league_id' => $league->id]);
        foreach ($teams as $team) {
            $team->categories()->attach($category->id, ['status' => 'approved']);
        }

        expect($league->canCreateTeam())->toBeTrue();
    });

    test('returns false for free plan when at limit', function () {
        $league = League::factory()->create(['plan' => Plan::FREE]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $teams = Team::factory()->count(8)->create(['league_id' => $league->id]);
        foreach ($teams as $team) {
            $team->categories()->attach($category->id, ['status' => 'approved']);
        }

        expect($league->canCreateTeam())->toBeFalse();
    });

    test('returns false for free plan when over limit', function () {
        $league = League::factory()->create(['plan' => Plan::FREE]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $teams = Team::factory()->count(10)->create(['league_id' => $league->id]);
        foreach ($teams as $team) {
            $team->categories()->attach($category->id, ['status' => 'approved']);
        }

        expect($league->canCreateTeam())->toBeFalse();
    });

    test('returns true for pro plan when under limit', function () {
        $league = League::factory()->create(['plan' => Plan::PRO]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $teams = Team::factory()->count(15)->create(['league_id' => $league->id]);
        foreach ($teams as $team) {
            $team->categories()->attach($category->id, ['status' => 'approved']);
        }

        expect($league->canCreateTeam())->toBeTrue();
    });

    test('returns false for pro plan when at limit', function () {
        $league = League::factory()->create(['plan' => Plan::PRO]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $teams = Team::factory()->count(20)->create(['league_id' => $league->id]);
        foreach ($teams as $team) {
            $team->categories()->attach($category->id, ['status' => 'approved']);
        }

        expect($league->canCreateTeam())->toBeFalse();
    });

    test('returns true for unlimited plan regardless of team count', function () {
        $league = League::factory()->create(['plan' => Plan::UNLIMITED]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        $teams = Team::factory()->count(50)->create(['league_id' => $league->id]);
        foreach ($teams as $team) {
            $team->categories()->attach($category->id, ['status' => 'approved']);
        }

        expect($league->canCreateTeam())->toBeTrue();
    });
});

describe('League canCreateCompetition Method', function () {

    test('returns false for free plan when at limit', function () {
        $league = League::factory()->create(['plan' => Plan::FREE]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        Competition::factory()->create(['category_id' => $category->id, 'league_id' => $league->id]);

        expect($league->canCreateCompetition())->toBeFalse();
    });

    test('returns true for pro plan when under limit', function () {
        $league = League::factory()->create(['plan' => Plan::PRO]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        Competition::factory()->count(3)->create(['category_id' => $category->id, 'league_id' => $league->id]);

        expect($league->canCreateCompetition())->toBeTrue();
    });

    test('returns false for pro plan when at limit', function () {
        $league = League::factory()->create(['plan' => Plan::PRO]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        Competition::factory()->count(5)->create(['category_id' => $category->id, 'league_id' => $league->id]);

        expect($league->canCreateCompetition())->toBeFalse();
    });

    test('returns true for unlimited plan regardless of competition count', function () {
        $league = League::factory()->create(['plan' => Plan::UNLIMITED]);
        $category = Category::factory()->create(['league_id' => $league->id]);
        Competition::factory()->count(20)->create(['category_id' => $category->id, 'league_id' => $league->id]);

        expect($league->canCreateCompetition())->toBeTrue();
    });
});
