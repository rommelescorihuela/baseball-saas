<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        }
        catch (\Exception $e) {
            return response()->json(['error' => 'Webhook signature verification failed.'], 400);
        }

        switch ($event->type) {
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                $this->updateSubscription($subscription);
                break;
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutCompleted($session);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    protected function updateSubscription($subscription)
    {
        $league = \App\Models\League::where('stripe_id', $subscription->customer)->first();
        if (!$league)
            return;

        $league->update([
            'subscription_status' => $subscription->status,
            'subscription_ends_at' => $subscription->cancel_at_period_end ?\Carbon\Carbon::createFromTimestamp($subscription->current_period_end) : null,
        ]);
    }

    protected function handleCheckoutCompleted($session)
    {
        $leagueId = $session->client_reference_id;
        $league = \App\Models\League::find($leagueId);

        if ($league) {
            $league->update([
                'stripe_id' => $session->customer,
                'subscription_status' => 'active',
            ]);
        }
    }
}