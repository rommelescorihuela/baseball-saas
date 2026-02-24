<?php

use App\Models\League;
use App\Models\Competition;
use App\Models\Category;
use App\Models\Team;
use App\Models\Season;

test('public pages are accessible and show correct data', function () {
    $league = League::factory()->create(['name' => 'Demo League', 'status' => 'active']);
    $season = Season::factory()->create(['league_id' => $league->id]);
    $category = Category::factory()->create(['league_id' => $league->id]);

    $competition = Competition::factory()->create([
        'season_id' => $season->id,
        'category_id' => $category->id,
        'name' => 'Summer Cup',
        'is_active' => true
    ]);

    $team = Team::factory()->create(['league_id' => $league->id, 'name' => 'Blue Jays']);
    $team->categories()->attach($category);

    // Check Home
    $this->get('/')->assertStatus(200)
        ->assertSee('Demo League');

    // Check Competition
    $this->get(route('public.competition.show', $competition->id))
        ->assertStatus(200)
        ->assertSee('Summer Cup');

    // Check Team
    $this->get(route('public.team.show', $team->id))
        ->assertStatus(200)
        ->assertSee('Blue Jays');
});