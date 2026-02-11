<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameEvent extends Model
{
    protected $fillable = [
        'game_id',
        'inning',
        'is_top_inning',
        'team_id',
        'batter_id',
        'pitcher_id',
        'outs_before',
        'balls_before',
        'strikes_before',
        'type',
        'result',
        'runs_scored',
    ];

    protected $casts = [
        'is_top_inning' => 'boolean',
        'result' => 'array',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function batter()
    {
        return $this->belongsTo(Player::class , 'batter_id');
    }

    public function pitcher()
    {
        return $this->belongsTo(Player::class , 'pitcher_id');
    }
}