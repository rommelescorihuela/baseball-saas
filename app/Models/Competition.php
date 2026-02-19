<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;
    protected $fillable = [
        'season_id',
        'category_id',
        'league_id',
        'name',
        'start_date',
        'end_date',
        'status',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class , 'category_team', 'category_id', 'team_id', 'category_id');
    }
}