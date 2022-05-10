<?php

namespace App\Services;

use App\Models\LicenceKey;
use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use function Safe\json_decode;

class RenewLicenceKey
{
    /**
     * Renew a licence key based on the payload received by Paddle.
     * We react to the webhook `subscription_payment_succeeded`.
     * We need to pass the payload as an array.
     *
     * @param  mixed  $payload
     * @return bool|null
     */
    public function execute(mixed $payload): ?bool
    {
        try {
            $plan = Plan::where('plan_id_on_paddle', $payload['subscription_plan_id'])
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return null;
        }

        // grab the user id that is stored on the passthrough array
        $userId = json_decode($payload['passthrough'], true);
        $userId = $userId['billable_id'];

        try {
            $licenceKey = LicenceKey::where('user_id', $userId)
                ->where('plan_id', $plan->id)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return null;
        }

        $licenceKey->subscription_state = 'subscription_payment_succeeded';
        $licenceKey->valid_until_at = $payload['next_bill_date'];
        $licenceKey->save();

        return true;
    }
}
