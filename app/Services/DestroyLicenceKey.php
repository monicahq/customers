<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyLicenceKey
{
    /**
     * Destroy a licence key based on the payload received by Paddle.
     * We react to the webhook `subscription_cancelled`.
     * We need to pass the payload as an array.
     *
     * @param  array  $payload
     * @return bool|null
     */
    public function execute(Subscription $subscription, array $payload): ?bool
    {
        $plan = $subscription->plan;

        /** @var \App\Models\User */
        $user = $subscription->billable;

        try {
            $licenceKey = $user->licenceKeys()
                ->where('plan_id', $plan->id)
                ->orderBy('created_at', 'desc')
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return null;
        }

        $licenceKey->subscription_state = 'subscription_cancelled';
        $licenceKey->valid_until_at = $payload['cancellation_effective_date'];
        $licenceKey->save();

        return true;
    }
}
