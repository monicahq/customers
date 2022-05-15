<?php

namespace App\Listeners;

use App\Models\Subscription;
use App\Models\User;
use App\Services\CreateLicenceKey;
use Laravel\Paddle\Events\SubscriptionCreated;

class SubscriptionCreatedListener
{
    /**
     * Handle Subscription Created webhooks.
     *
     * @param  SubscriptionCreated  $event
     * @return void
     */
    public function handle(SubscriptionCreated $event)
    {
        if (! $event->billable instanceof User) {
            return; // @codeCoverageIgnore
        }
        if (! $event->subscription instanceof Subscription) {
            return; // @codeCoverageIgnore
        }

        app(CreateLicenceKey::class)->execute($event->billable, $event->subscription, $event->payload);
    }
}
