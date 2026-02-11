<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'name', 'last_name', 'number', 'date_of_birth', 'position'];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
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