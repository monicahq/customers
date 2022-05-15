<?php

namespace App\Listeners;

use App\Models\Subscription;
use App\Services\DestroyLicenceKey;
use Laravel\Paddle\Events\SubscriptionCancelled;

class SubscriptionCancelledListener
{
    /**
     * Handle Subscription Created webhooks.
     *
     * @param  SubscriptionCancelled  $event
     * @return void
     */
    public function handle(SubscriptionCancelled $event)
    {
        if (! $event->subscription instanceof Subscription) {
            return;
        }
        app(DestroyLicenceKey::class)->execute($event->subscription, $event->payload);
    }
}
