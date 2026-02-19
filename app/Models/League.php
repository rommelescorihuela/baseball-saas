<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class League extends Model
{
    use HasFactory, Billable;
    protected $fillable = ['name', 'slug', 'status', 'logo', 'stripe_id', 'plan', 'subscription_status', 'trial_ends_at', 'subscription_ends_at'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $casts = [
        'plan' => \App\Enums\Plan::class ,
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public function isSubscribed(): bool
    {
        if ($this->plan === \App\Enums\Plan::FREE) {
            return true; // Technically "subscribed" to free plan
        }

        if ($this->subscription_status === 'active' || $this->subscription_status === 'trialing') {
            return true;
        }

        return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }

    public function canCreateTeam(): bool
    {
        $limit = $this->plan->maxTeams();
        if ($limit === null)
            return true;

        return $this->teams()->count() < $limit;
    }

    public function canCreateCompetition(): bool
    {
        $limit = $this->plan->maxCompetitions();
        if ($limit === null)
            return true;

        // Count competitions across all categories or just total competitions?
        // Let's assume global competition limit for the league
        return \App\Models\Competition::whereHas('category', function ($q) {
            $q->where('league_id', $this->id);
        })->count() < $limit;
    }

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