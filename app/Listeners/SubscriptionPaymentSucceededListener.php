<?php

namespace App\Listeners;

use App\Services\RenewLicenceKey;
use Laravel\Paddle\Events\SubscriptionPaymentSucceeded;

class SubscriptionPaymentSucceededListener
{
    /**
     * Handle Subscription Created webhooks.
     *
     * @param  SubscriptionPaymentSucceeded  $event
     * @return void
     */
    public function handle(SubscriptionPaymentSucceeded $event)
    {
        app(RenewLicenceKey::class)->execute($event->billable, $event->receipt, $event->payload);
    }
}
