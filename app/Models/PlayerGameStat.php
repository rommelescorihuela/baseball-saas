<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerGameStat extends Model
{
    protected $guarded = [];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}