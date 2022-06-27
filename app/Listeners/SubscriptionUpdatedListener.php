<?php

namespace App\Listeners;

use App\Models\Subscription;
use App\Services\RenewLicenceKey;
use Laravel\Paddle\Events\SubscriptionUpdated;

class SubscriptionUpdatedListener
{
    /**
     * Handle Subscription Updated webhooks.
     *
     * @param  SubscriptionUpdated  $event
     * @return void
     */
    public function handle(SubscriptionUpdated $event)
    {
        if (! $event->subscription instanceof Subscription) {
            return; // @codeCoverageIgnore
        }

        app(RenewLicenceKey::class)->execute($event->subscription->billable, $event->subscription, $event->payload);
    }
}
