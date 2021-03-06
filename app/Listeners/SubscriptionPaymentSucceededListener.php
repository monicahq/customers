<?php

namespace App\Listeners;

use App\Models\Receipt;
use App\Models\User;
use App\Services\RenewLicenceKey;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            return; // @codeCoverageIgnore
        }

        if (! $event->receipt instanceof Receipt) {
            return; // @codeCoverageIgnore
        }

        if ($event->receipt->billable_id !== $event->billable->id) {
            throw new ModelNotFoundException(); // @codeCoverageIgnore
        }

        /** @var \App\Models\Subscription */
        $subscription = $event->receipt->subscription;

        app(RenewLicenceKey::class)->execute($event->billable, $subscription, $event->payload);
    }
}
