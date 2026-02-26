<?php
$player = App\Models\Player::first();
if (!$player) {
    $team = App\Models\Team::first();
    if (!$team) {
        $league = App\Models\League::first();
        if (!$league) {
            $league = App\Models\League::create(['name' => 'Demo League', 'slug' => 'demo-league', 'subscription_status' => 'active']);
        }
        $competition = App\Models\Competition::first();
        if (!$competition) {
            $competition = App\Models\Competition::create(['name' => 'Demo Competition', 'league_id' => $league->id]);
        }
        $team = App\Models\Team::create(['name' => 'Demo Team', 'competition_id' => $competition->id, 'league_id' => $league->id]);
    }
    $player = App\Models\Player::create([
        'team_id' => $team->id,
        'first_name' => 'Demo',
        'last_name' => 'Player',
        'number' => '99',
        'position' => 'CF',
        'status' => 'active',
        'bats' => 'R',
        'throws' => 'R',
        'league_id' => $team->league_id,
    ]);
}
echo "Player ID: " . $player->id . "\n";
echo "League ID: " . $player->league_id . "\n";
