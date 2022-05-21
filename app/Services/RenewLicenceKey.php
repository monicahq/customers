<?php

namespace App\Services;

use App\Models\Receipt;
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
     * @param  array  $payload
     * @return bool|null
     */
    public function execute(User $user, Receipt $receipt, array $payload): ?bool
    {
        if ($receipt->billable_id !== $user->id) {
            throw new ModelNotFoundException;
        }

        /** @var \App\Models\Subscription */
        $subscription = $receipt->subscription;

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
        $licenceKey->save();

        return true;
    }
}
