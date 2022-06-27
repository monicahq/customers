<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Laravel\Paddle\Events\WebhookReceived;

class WebhookReceivedListener
{
    /**
     * Handle Paddle webhook event.
     *
     * @param  WebhookReceived  $event
     * @return void
     */
    public function handle(WebhookReceived $event)
    {
        Log::info("{$event->payload['alert_name']} paddle webhook received", [
            'payload' => $event->payload,
        ]);
    }
}
