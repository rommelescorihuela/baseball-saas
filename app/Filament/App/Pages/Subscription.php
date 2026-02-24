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

    public function subscribe(string $planName)
    {
        $plan = Plan::tryFrom($planName);
        if (!$plan || !$plan->stripePriceId()) {
            return;
        }

        $league = Filament::getTenant();

        // If already on this plan, do nothing or show message
        if ($league->subscribed('default') && $league->subscription('default')->hasPrice($plan->stripePriceId())) {
            return;
        }
        
        // If already subscribed to another plan, swap
        if ($league->subscribed('default')) {
            $league->subscription('default')->swap($plan->stripePriceId());
            return redirect()->route('filament.app.pages.subscription');
        }

        // New Subscription
        return $league->newSubscription('default', $plan->stripePriceId())
            ->checkout([
                'success_url' => route('filament.app.pages.subscription', ['tenant' => $league]),
                'cancel_url' => route('filament.app.pages.subscription', ['tenant' => $league]),
            ]);
    }

    public function manage()
    {
        $league = Filament::getTenant();
        return $league->billingPortalUrl(route('filament.app.pages.subscription', ['tenant' => $league]));
    }

    protected function getViewData(): array
    {
        $league = Filament::getTenant();
        
        return [
            'plans' => Plan::cases(),
            'currentPlan' => $league->subscribed('default') ? $league->subscription('default')->type : 'free', // Or derive from stripe price
            'isSubscribed' => $league->subscribed('default'),
            'onGracePeriod' => $league->subscribed('default') && $league->subscription('default')->onGracePeriod(),
        ];
    }
}
