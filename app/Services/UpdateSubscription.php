<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

class UpdateSubscription extends BaseService
{
    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Update user address.
     *
     * @param  array  $data
     * @return Subscription
     */
    public function execute(array $data): Subscription
    {
        $this->validateRules($data);

        $user = User::findOrFail($data['user_id']);
        $plan = Plan::findOrFail($data['plan_id']);

        // Get active subscriptions with the same plan product as the new plan
        $subscription = $user->subscriptions()
            ->active()
            ->notCancelled()
            ->product($plan->product)
            ->firstOrFail();

        if ($subscription->paddle_plan !== $plan->plan_id_on_paddle) {
            $subscription = $subscription->swapAndInvoice($plan->plan_id_on_paddle);
        }

        if (isset($data['quantity']) && $subscription->quantity !== $data['quantity']) {
            $subscription->updateQuantity($data['quantity']);
        }

        return $subscription;
    }
}
