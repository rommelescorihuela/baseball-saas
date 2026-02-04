<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;


class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'league_id',
        'team_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $requestId = request('_debug_request_id') ?? 'UNKNOWN';
        \Illuminate\Support\Facades\Log::info("[$requestId] Checking access for user: " . $this->email);
        
        // Super admins have full access
        if ($this->hasRole('super-admin')) {
            \Illuminate\Support\Facades\Log::info("[$requestId] User has super-admin role. Access granted.");
            return true;
        }

        // Team owners can only access their team's subdomain
        if ($this->hasRole('team-owner')) {
            \Illuminate\Support\Facades\Log::info("[$requestId] User has team-owner role. Checking team assignment.", [
                'user_team_id' => $this->team_id,
            ]);
            
            // Get the current team from the request (set by ResolveTenant middleware)
            $host = request()->getHost();
            $subdomain = explode('.', $host)[0] ?? '';
            
            \Illuminate\Support\Facades\Log::info("[$requestId] Subdomain detected: " . $subdomain);
            
            // Find the team by subdomain
            $team = \App\Models\Team::where('subdomain', $subdomain)->first();
            
            if ($team && $this->team_id === $team->id) {
                \Illuminate\Support\Facades\Log::info("[$requestId] Access granted for team-owner. Team match confirmed.");
                return true;
            }
            
            \Illuminate\Support\Facades\Log::info("[$requestId] Access denied. Team mismatch.", [
                'user_team_id' => $this->team_id,
                'subdomain_team_id' => $team?->id,
            ]);
        }
        
        \Illuminate\Support\Facades\Log::info("[$requestId] Access denied.");

        return false;
    }
}