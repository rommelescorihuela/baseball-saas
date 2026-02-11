<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'status', 'logo'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }
}