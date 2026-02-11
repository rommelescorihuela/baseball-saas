<?php

namespace App\Filament\App\Pages;

use App\Models\League;
use App\Enums\Plan;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class Subscription extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected string $view = 'filament.app.pages.subscription';

    protected static ?string $title = 'Mi Suscripción';

    public League $league;

    public function mount()
    {
        $this->league = Filament::getTenant();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upgrade_pro')
                ->label('Mejorar a Pro')
                ->color('success')
                ->hidden(fn () => $this->league->plan === Plan::PRO || $this->league->plan === Plan::UNLIMITED)
                ->action(fn () => $this->checkout(Plan::PRO)),

            Action::make('upgrade_unlimited')
                ->label('Mejorar a Unlimited')
                ->color('primary')
                ->hidden(fn () => $this->league->plan === Plan::UNLIMITED)
                ->action(fn () => $this->checkout(Plan::UNLIMITED)),
        ];
    }

    public function checkout(Plan $plan)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        // Mock price IDs - In real app, these would be in Config or Plan Enum
        $priceId = match($plan) {
            Plan::PRO => 'price_pro_placeholder',
            Plan::UNLIMITED => 'price_unlimited_placeholder',
            default => null,
        };

        if (!$priceId) {
            Notification::make()->title('Error en el plan seleccionado')->danger()->send();
            return;
        }

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('filament.app.pages.subscription', ['tenant' => $this->league->slug]) . '?success=true',
                'cancel_url' => route('filament.app.pages.subscription', ['tenant' => $this->league->slug]) . '?cancel=true',
                'client_reference_id' => $this->league->id,
                'customer_email' => $this->league->users()->first()?->email,
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al conectar con Stripe')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}