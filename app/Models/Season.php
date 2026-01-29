<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'league_id',
        'name',
        'start_date',
        'end_date'
    ];

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }
}
