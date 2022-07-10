<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

class RenewLicenceKey
{
    /**
     * Renew a licence key based on the payload received by Paddle.
     * We react to the webhook `subscription_payment_succeeded`.
     * We need to pass the payload as an array.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Subscription  $subscription
     * @param  array  $payload
     * @return bool|null
     */
    public function execute(User $user, Subscription $subscription, array $payload): ?bool
    {
        if ($subscription->billable_id !== $user->id) {
            throw new ModelNotFoundException;
        }

        try {
            $licenceKey = $user->licenceKeys()
                ->where('plan_id', $subscription->plan->id)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return null;
        }

        $licenceKey->subscription_state = 'subscription_payment_succeeded';
        $licenceKey->valid_until_at = Carbon::parse($payload['next_bill_date']);

        if (isset($payload['update_url'])) {
            $licenceKey->paddle_update_url = $payload['update_url'];
        }
        if (isset($payload['cancel_url'])) {
            $licenceKey->paddle_cancel_url = $payload['cancel_url'];
        }

        $licenceKey->save();

        return true;
    }
}
