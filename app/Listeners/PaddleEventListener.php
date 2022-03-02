<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use App\Models\Plan;
use App\Models\LicenceKey;
use Laravel\Paddle\Events\WebhookReceived;

class PaddleEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle received Paddle webhooks.
     *
     * @param  WebhookReceived  $event
     * @return void
     */
    public function handle(WebhookReceived $event)
    {
        if ($event->payload['alert_name'] === 'subscription_payment_succeeded') {
            $plan = Plan::where('plan_id_on_paddle', $event->payload['subscription_plan_id'])->first();

            $billableId = json_decode($event->payload['passthrough'], true);
            $billableId = $billableId['billable_id'];
            Log::info($billableId);

            $licenceKey = LicenceKey::where('user_id', $billableId)
                ->where('plan_id', $plan->id)
                ->first();

            $licenceKey->update([
                'subscription_state' => 'subscription_payment_succeeded',
                'valid_until_at' => $event->payload['next_bill_date'],
            ]);
        }

        if ($event->payload['alert_name'] === 'subscription_cancelled') {
            $plan = Plan::where('plan_id_on_paddle', $event->payload['subscription_plan_id'])->first();

            $licenceKey = LicenceKey::where('user_id', $event->payload['passthrough']['billable_id'])
                ->where('plan_id', $plan->id)
                ->first();

            $licenceKey->update([
                'subscription_state' => 'subscription_cancelled',
                'valid_until_at' => $event->payload['cancellation_effective_date'],
            ]);
        }
    }
}
