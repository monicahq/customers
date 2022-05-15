<?php

namespace App\Listeners;

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
        app(CreateLicenceKey::class)->execute($event->billable, $event->subscription, $event->payload);
    }
}
