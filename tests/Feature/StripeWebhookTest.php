<?php

namespace Tests\Feature;

use App\Enums\Plan;
use App\Listeners\StripeEventListener;
use App\Models\League;
use Laravel\Cashier\Events\WebhookReceived;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    public function test_listener_updates_league_plan_on_subscription_update()
    {
        // 1. Setup League
        $stripeId = 'cus_test_' . uniqid();
        $league = League::create([
            'name' => 'Test League',
            'slug' => 'test-league-' . uniqid(),
            'status' => 'active',
            'plan' => 'free',
            'stripe_id' => $stripeId,
        ]);

        // 2. Mock Payload for Subscription Update (Pro Plan)
        // We need a dummy price ID that matches PRO plan environment variable or mock the Enum?
        // Mocking env vars in test is possible.
        // Let's assume STRIPE_PRICE_PRO is set or we set it dynamically.
        
        $priceId = 'price_pro_test';
        config(['services.stripe.price_pro' => $priceId]); // Assuming I use config/services, but I used env() in Enum.
        // Enums are loaded early. Mocking env() for Enum might be hard if already loaded.
        // But `stripePriceId()` calls `env()` at runtime.
        // `fromPriceId` calls `stripePriceId()`.
        
        // I need to override the env var.
        // `putenv('STRIPE_PRICE_PRO=' . $priceId);` might work if not cached.
        // But `env()` in Laravel might implicitly use `$_ENV` or `getenv`.
        
        // Better: Check what `Plan::PRO->stripePriceId()` returns.
        // If it returns null/empty, I can't match it.
        // I should set the env var in `phpunit.xml` or runtime.
        
        // For this test, let's try to set the env var.
        
        $_ENV['STRIPE_PRICE_PRO'] = $priceId;
        
        $payload = [
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'customer' => $stripeId,
                    'status' => 'active',
                    'items' => [
                        'data' => [
                            [
                                'price' => ['id' => $priceId]
                            ]
                        ]
                    ],
                    'current_period_end' => now()->addMonth()->timestamp,
                ]
            ]
        ];

        // 3. Dispatch Event
        $event = new WebhookReceived($payload);
        $listener = new StripeEventListener();
        $listener->handle($event);

        // 4. Assert
        $league->refresh();
        $this->assertEquals(Plan::PRO, $league->plan);
        $this->assertEquals('active', $league->subscription_status);
    }
    
    public function test_listener_cancels_subscription()
    {
         // 1. Setup League
        $stripeId = 'cus_test_cancel_' . uniqid();
        $league = League::create([
            'name' => 'Test League',
            'slug' => 'test-league-' . uniqid(),
            'status' => 'active',
            'plan' => 'pro',
            'stripe_id' => $stripeId,
        ]);

        $payload = [
            'type' => 'customer.subscription.deleted',
            'data' => [
                'object' => [
                    'customer' => $stripeId,
                    'status' => 'canceled',
                ]
            ]
        ];

        // 3. Dispatch Event
        $event = new WebhookReceived($payload);
        $listener = new StripeEventListener();
        $listener->handle($event);

        // 4. Assert
        $league->refresh();
        $this->assertEquals(Plan::FREE, $league->plan);
        $this->assertEquals('canceled', $league->subscription_status);
    }
}
