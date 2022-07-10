<?php

namespace App\Services;

use App\Helpers\Products;
use App\Models\LicenceKey;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateLicenceKey
{
    private User $user;
    private Plan $plan;
    private LicenceKey $licenceKey;
    private Carbon $nextDate;
    private string $updateUrl;
    private string $cancelUrl;

    /**
     * Create an instance key.
     * We react to the webhook `subscription_created`.
     *
     * @param  array  $payload
     * @return LicenceKey|null
     */
    public function execute(User $user, Subscription $subscription, array $payload): ?LicenceKey
    {
        if ($subscription->billable_id !== $user->id) {
            throw new ModelNotFoundException;
        }

        $this->plan = $subscription->plan;
        $this->user = $user;

        $this->nextDate = Carbon::parse($payload['next_bill_date']);
        $this->cancelUrl = $payload['cancel_url'];
        $this->updateUrl = $payload['update_url'];

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
     * @return array
     */
    private function generateKey(): array
    {
        $plans = Plan::where('product', $this->plan->product)->get();

        $productIds = $plans->pluck('plan_id_on_paddle');
        $prices = app(Products::class)->getProductPrices($productIds);

        $price = $prices->where('product_id', $this->plan->plan_id_on_paddle)->first();

        return [
            'frequency' => $price['frequency'],
            'purchaser_email' => $this->user->email,
        ];
    }

    private function encodeKey(): void
    {
        $key = $this->generateKey();
        $encrypter = app('license.encrypter');

        $this->licenceKey = LicenceKey::create([
            'plan_id' => $this->plan->id,
            'user_id' => $this->user->id,
            'key' => $encrypter->encrypt($key),
            'valid_until_at' => $this->nextDate,
            'subscription_state' => 'subscription_created',
            'paddle_update_url' => $this->updateUrl,
            'paddle_cancel_url' => $this->cancelUrl,
        ]);
    }
}
