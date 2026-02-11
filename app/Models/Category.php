<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['competition_id', 'name'];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class , 'category_team');
    }
}