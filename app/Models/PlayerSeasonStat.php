<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerSeasonStat extends Model
{
    protected $guarded = [];

    public function getAvgAttribute()
    {
        if ($this->ab == 0)
            return '.000';

        $avg = $this->h / $this->ab;
        return number_format($avg, 3, '.', ''); // .350
    }
}