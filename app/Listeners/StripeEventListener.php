<?php

namespace App\Listeners;

use Laravel\Cashier\Events\WebhookReceived;
use App\Models\League;
use App\Enums\Plan;
use Illuminate\Support\Facades\Log;

class StripeEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;
        $type = $payload['type'] ?? null;

        if (!$type) {
            return;
        }

        if (in_array($type, ['customer.subscription.updated', 'customer.subscription.created', 'customer.subscription.deleted'])) {
            $subscription = $payload['data']['object'];
            $customerId = $subscription['customer'];

            $league = League::where('stripe_id', $customerId)->first();

            if ($league) {
                if ($type === 'customer.subscription.deleted') {
                    $league->update([
                        'plan' => Plan::FREE,
                        'subscription_status' => 'canceled',
                        'subscription_ends_at' => now(), // Or from event
                    ]);
                    Log::info("League {$league->id} subscription canceled.");
                } else {
                    $priceId = $subscription['items']['data'][0]['price']['id'] ?? null;
                    $status = $subscription['status'];
                    
                    // Map Price ID to Plan
                    $plan = Plan::fromPriceId($priceId);

                    if ($plan) {
                        $league->update([
                            'plan' => $plan,
                            'subscription_status' => $status,
                            'subscription_ends_at' => isset($subscription['current_period_end']) 
                                ? \Carbon\Carbon::createFromTimestamp($subscription['current_period_end']) 
                                : null,
                        ]);
                        Log::info("League {$league->id} updated to plan {$plan->value} status {$status}.");
                    } else {
                         // Fallback if price ID not found (e.g. unknown plan), maybe just update status but keep existing plan?
                         // Or log warning.
                         Log::warning("Unknown price ID {$priceId} for League {$league->id}.");
                         $league->update(['subscription_status' => $status]);
                    }
                }
            }
        }
    }
}
