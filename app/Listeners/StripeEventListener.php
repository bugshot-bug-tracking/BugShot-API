<?php

namespace App\Listeners;

use Laravel\Cashier\Events\WebhookReceived;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StripeEventListener
{
    /**
     * Handle received Stripe webhooks.
     */
    public function handle(WebhookReceived $event)
    {
		Log::debug("Webhook event received.");
        // if ($event->payload['type'] === 'checkout.session.completed') {

        //     return new Response('Webhook Handled', 200);
        // }
    }
}
