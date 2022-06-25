<?php

namespace App\Services;

use App\Models\Plan;
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
        ];
    }

    /**
     * Update user address.
     *
     * @param  array  $data
     * @return User
     */
    public function execute(array $data)
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

        return $subscription->swapAndInvoice($plan->plan_id_on_paddle);
    }
}
