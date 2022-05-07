<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Models\LicenceKey;
use function Safe\json_decode;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateLicenceKey
{
    private User $user;
    private Plan $plan;
    private LicenceKey $licenceKey;
    private Collection $key;
    private Carbon $nextDate;
    private string $updateUrl;
    private string $cancelUrl;

    /**
     * Create an instance key.
     * We react to the webhook `subscription_created`.
     *
     * @param  mixed  $payload
     * @return LicenceKey|null
     */
    public function execute(mixed $payload): ?LicenceKey
    {
        try {
            $this->plan = Plan::where('plan_id_on_paddle', $payload['subscription_plan_id'])
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return null;
        }

        // grab the user id that is stored on the passthrough array
        $userId = json_decode($payload['passthrough'], true);
        $userId = $userId['billable_id'];

        try {
            $this->user = User::findOrFail($userId);
        } catch (ModelNotFoundException) {
            return null;
        }

        $this->nextDate = Carbon::parse($payload['next_bill_date']);
        $this->cancelUrl = $payload['cancel_url'];
        $this->updateUrl = $payload['update_url'];

        $this->generateKey();
        $this->encodeKey();

        return $this->licenceKey;
    }

    /**
     * The key is a json that contains:
     * - frequency: monthly|yearly
     * - number of max users,
     * - the date the next check should occured,
     * - the email address of the user who purchased the license.
     *
     * @return void
     */
    private function generateKey(): void
    {
        $this->key = collect([
            'frequency' => $this->plan->frequency,
            'purchaser_email' => $this->user->email,
            'next_check_at' => $this->nextDate,
        ]);
    }

    private function encodeKey(): void
    {
        $key = $this->key->toJson();
        $key = base64_encode($key.config('customers.private_key_to_encrypt_licence_keys'));

        $this->licenceKey = LicenceKey::create([
            'plan_id' => $this->plan->id,
            'user_id' => $this->user->id,
            'key' => $key,
            'valid_until_at' => $this->nextDate,
            'subscription_state' => 'subscription_created',
            'paddle_update_url' => $this->updateUrl,
            'paddle_cancel_url' => $this->cancelUrl,
        ]);
    }
}
