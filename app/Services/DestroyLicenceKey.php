<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\LicenceKey;
use function Safe\json_decode;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyLicenceKey
{
    /**
     * Destroy a licence key based on the payload received by Paddle.
     * We react to the webhook `subscription_cancelled`.
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
