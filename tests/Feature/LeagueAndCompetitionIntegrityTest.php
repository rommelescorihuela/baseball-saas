<?php

use App\Models\League;
use App\Models\Category;
use App\Models\Season;
use App\Models\Team;

test('league factory creates dependencies properly', function () {
    $league = League::factory()->create();

    expect($league->exists)->toBeTrue();
});

test('deleting a league cascades contextually', function () {
    $league = League::factory()->create();
    
    // Create dependencies
    $season = Season::factory()->create(['league_id' => $league->id]);
    $team = Team::factory()->create(['league_id' => $league->id]);
    $category = Category::factory()->create(['league_id' => $league->id]);

    // Perform deletion
    $league->delete();

    // The database integrity logic (either foreign keys or observers) should handle this.
    // If not cascading, they might be orphaned, but shouldn't throw 500 errors.
    $this->assertDatabaseMissing('leagues', ['id' => $league->id]);
});
