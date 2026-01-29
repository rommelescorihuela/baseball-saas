<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\League;
use App\Models\User;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'league_id',
        'name',
        'category',
        'subdomain',
    ];

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }
}
