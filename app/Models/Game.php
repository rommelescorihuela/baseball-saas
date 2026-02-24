<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'league_id',
        'competition_id',
        'home_team_id',
        'visitor_team_id',
        'start_time',
        'location',
        'status',
        'home_score',
        'visitor_score',
    ];

    protected $casts = [
        'start_time' => 'datetime',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class , 'home_team_id');
    }

    public function visitorTeam()
    {
        return $this->belongsTo(Team::class , 'visitor_team_id');
    }

    public function events()
    {
        return $this->hasMany(GameEvent::class);
    }

    public function stats()
    {
        return $this->hasMany(PlayerGameStat::class);
    }
}