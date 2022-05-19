<?php

namespace App\Services;

use App\Models\User;

class DestroyAccount extends BaseService
{
    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
        ];
    }

    /**
     * Destroy an account and cancel all the active subscriptions.
     *
     * @param  array  $data
     * @return void
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $user = User::findOrFail($data['user_id']);

        $this->deleteProfilePhoto($user);
        $this->deleteTokens($user);
        $this->cancelSubscriptions($user);

        $user->delete();
    }

    private function deleteProfilePhoto($user)
    {
        $user->deleteProfilePhoto();
    }

    private function deleteTokens($user)
    {
        $user->tokens->each->delete();
    }

    private function cancelSubscriptions($user)
    {
        $licences = $user->licenceKeys()
            ->with('plan')
            ->where('subscription_state', '!=', 'subscription_cancelled')
            ->get();

        foreach ($licences as $licence) {
            $plan = $licence->plan->plan_name;

            $user->subscription($plan)->cancel();
        }
    }
}
