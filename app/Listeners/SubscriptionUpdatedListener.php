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

        /** @var \App\Models\User */
        $user = $event->subscription->billable;

        app(RenewLicenceKey::class)->execute($user, $event->subscription, $event->payload);
    }
}
