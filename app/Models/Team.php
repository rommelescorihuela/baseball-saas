<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'logo', 'league_id'];

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user');
    }

    public function players()
    {
        return $this->belongsToMany(Player::class , 'team_player_season')->withPivot('season_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class , 'category_team');
    }

    public function homeGames()
    {
        return $this->hasMany(Game::class , 'home_team_id');
    }

    public function visitorGames()
    {
        return $this->hasMany(Game::class , 'visitor_team_id');
    }
}