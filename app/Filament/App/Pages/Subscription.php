<?php

namespace App\Filament\App\Pages;

use App\Enums\Plan;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Subscription extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    protected string $view = 'filament.app.pages.subscription';

    protected static ?string $navigationLabel = 'Suscripción';

    protected static ?string $title = 'Suscripción y Pagos';

    public static function canAccess(): bool
    {
        $tenant = Filament::getTenant();
        // Only League Owner can manage subscription
        return $tenant && Auth::user()->hasRole('league_owner') && $tenant->users()->where('user_id', Auth::id())->exists();
    }

    protected function getViewData(): array
    {
        $league = Filament::getTenant();

        return [
            'plans' => Plan::cases(),
            'currentPlan' => $league->plan,
            'status' => $league->subscription_status,
            'expiration' => $league->subscription_ends_at,
        ];
    }
}
