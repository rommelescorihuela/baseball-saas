<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'game_id',
        'at_bats',
        'hits',
        'runs',
        'home_runs',
        'rbis',
        'walks',
        'strikeouts',
        'innings_pitched',
        'strikeouts_pitched',
        'runs_allowed'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
