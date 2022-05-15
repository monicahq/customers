<?php

namespace App\Listeners;

use App\Models\Receipt;
use App\Models\User;
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
        if (! $event->billable instanceof User) {
            return;
        }
        if (! $event->receipt instanceof Receipt) {
            return;
        }

        app(RenewLicenceKey::class)->execute($event->billable, $event->receipt, $event->payload);
    }
}
