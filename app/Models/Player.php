<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'league_id', 'name', 'last_name', 'number', 'date_of_birth', 'position', 'created_by'];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Usuario que creó el registro (Secretaría/Coach)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Equipos a los que pertenece el jugador (a través de temporadas)
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_player_season')
            ->withPivot('season_id', 'number', 'position')
            ->withTimestamps();
    }

    public function stats()
    {
        return $this->hasMany(PlayerSeasonStat::class);
    }

    // Helper to get latest stats (or current season if we had context)
    public function currentStats()
    {
        return $this->hasOne(PlayerSeasonStat::class)->latestOfMany();
    }
}
