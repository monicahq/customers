<?php

namespace App\Listeners;

use App\Services\CreateLicenceKey;
use App\Services\DestroyLicenceKey;
use App\Services\RenewLicenceKey;
use Laravel\Paddle\Events\WebhookReceived;

class PaddleEventListener
{
    /**
     * Handle received Paddle webhooks.
     *
     * @param  WebhookReceived  $event
     * @return void
     */
    public function handle(WebhookReceived $event)
    {
        switch ($event->payload['alert_name']) {
            case 'subscription_created':
                (new CreateLicenceKey)->execute($event->payload);
                break;

            case 'subscription_payment_succeeded':
                (new RenewLicenceKey)->execute($event->payload);
                break;

            case 'subscription_cancelled':
                (new DestroyLicenceKey)->execute($event->payload);
                break;

            default:
                // Do nothing
                break;
        }
    }
}
