<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class League extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'status', 'logo', 'plan', 'subscription_status', 'trial_ends_at', 'subscription_ends_at'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $casts = [
        'plan' => \App\Enums\Plan::class,
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public function isSubscribed(): bool
    {
        if ($this->plan === \App\Enums\Plan::FREE) {
            return true;
        }

        // Active or Trialing is immediate access
        if ($this->subscription_status === 'active' || $this->subscription_status === 'trialing') {
            return true;
        }

        // If canceled or past_due, it only works if the expiration date is still in the future
        return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }

    public function approvedTeamsCount(): int
    {
        return \App\Models\Team::whereHas('categories', function ($query) {
            $query->where('league_id', $this->id)
                ->where('category_team.status', 'approved');
        })->count();
    }

    public function canApproveTeam(): bool
    {
        $limit = $this->plan->maxTeams();
        if ($limit === null)
            return true;

        return $this->approvedTeamsCount() < $limit;
    }

    public function canCreateTeam(): bool
    {
        return $this->canApproveTeam();
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

    public function canCreateCategory(): bool
    {
        $limit = $this->plan->maxCategories();
        if ($limit === null)
            return true;

        return \App\Models\Category::where('league_id', $this->id)->count() < $limit;
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