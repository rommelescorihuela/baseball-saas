<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerGameStat extends Model
{
    protected $guarded = [];

    protected $casts = [
        'ip' => 'decimal:1',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Batting Calculated Attributes
    |--------------------------------------------------------------------------
    */

    /**
     * Batting Average: H / AB
     */
    public function getAvgAttribute(): string
    {
        if ($this->ab == 0) {
            return '.000';
        }

        return number_format($this->h / $this->ab, 3, '.', '');
    }

    /**
     * On-Base Percentage: (H + BB + HBP) / (AB + BB + HBP + SF)
     */
    public function getObpAttribute(): string
    {
        $numerator = $this->h + $this->bb + $this->hbp;
        $denominator = $this->ab + $this->bb + $this->hbp + $this->sacrifice_flies;

        if ($denominator == 0) {
            return '.000';
        }

        return number_format($numerator / $denominator, 3, '.', '');
    }

    /**
     * Slugging Percentage: (1B + 2*2B + 3*3B + 4*HR) / AB
     */
    public function getSlgAttribute(): string
    {
        if ($this->ab == 0) {
            return '.000';
        }

        $totalBases = $this->h + $this->doubles + (2 * $this->triples) + (3 * $this->hr);

        return number_format($totalBases / $this->ab, 3, '.', '');
    }

    /**
     * On-Base Plus Slugging
     */
    public function getOpsAttribute(): string
    {
        return number_format((float) $this->obp + (float) $this->slg, 3, '.', '');
    }

    /*
    |--------------------------------------------------------------------------
    | Pitching Calculated Attributes
    |--------------------------------------------------------------------------
    */

    /**
     * Earned Run Average: (ER / IP) * 9
     */
    public function getEraAttribute(): string
    {
        if ($this->ip == 0) {
            return '0.00';
        }

        return number_format(($this->p_er / $this->ip) * 9, 2, '.', '');
    }

    /**
     * Walks plus Hits per Inning Pitched: (H + BB) / IP
     */
    public function getWhipAttribute(): string
    {
        if ($this->ip == 0) {
            return '0.00';
        }

        return number_format(($this->p_h + $this->p_bb) / $this->ip, 2, '.', '');
    }

    /**
     * Opponent Batting Average: H / (IP * 3)
     */
    public function getOppAvgAttribute(): string
    {
        $battersFaced = $this->ip * 3; // Approximate
        if ($battersFaced == 0) {
            return '.000';
        }

        return number_format($this->p_h / $battersFaced, 3, '.', '');
    }

    /**
     * Strikeouts per 9 innings
     */
    public function getSo9Attribute(): string
    {
        if ($this->ip == 0) {
            return '0.0';
        }

        return number_format(($this->p_so / $this->ip) * 9, 1, '.', '');
    }

    /**
     * Walks per 9 innings
     */
    public function getBb9Attribute(): string
    {
        if ($this->ip == 0) {
            return '0.0';
        }

        return number_format(($this->p_bb / $this->ip) * 9, 1, '.', '');
    }
}
