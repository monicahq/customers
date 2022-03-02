<?php

namespace App\Listeners;

use App\Models\Plan;
use App\Models\LicenceKey;
use App\Services\CreateLicenceKey;
use App\Services\DestroyLicenceKey;
use App\Services\RenewLicenceKey;
use Illuminate\Support\Facades\Log;
use Laravel\Paddle\Events\WebhookReceived;

class PaddleEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle received Paddle webhooks.
     *
     * @param  WebhookReceived  $event
     * @return void
     */
    public function handle(WebhookReceived $event)
    {
        if ($event->payload['alert_name'] === 'subscription_created') {
            (new CreateLicenceKey)->execute($event->payload);
        }

        if ($event->payload['alert_name'] === 'subscription_payment_succeeded') {
            (new RenewLicenceKey)->execute($event->payload);
        }

        if ($event->payload['alert_name'] === 'subscription_cancelled') {
            (new DestroyLicenceKey)->execute($event->payload);
        }
    }
}
