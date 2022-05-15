<?php

namespace App\Listeners;

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
        app(DestroyLicenceKey::class)->execute($event->subscription, $event->payload);
    }
}
