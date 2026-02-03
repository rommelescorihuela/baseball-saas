<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Team;


class League extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'slug',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
