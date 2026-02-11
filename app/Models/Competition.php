<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;
    protected $fillable = ['season_id', 'name', 'status', 'start_date', 'end_date'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}