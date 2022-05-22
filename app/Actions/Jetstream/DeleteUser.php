<?php

namespace App\Actions\Jetstream;

use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     *
     * @param  mixed  $user
     * @return void
     */
    public function delete($user)
    {
        DB::transaction(function () use ($user) {
            $user->tokens->each->delete();
            $this->cancelSubscriptions($user);
            $user->delete();
        });
    }

    protected function cancelSubscriptions($user)
    {
        $subscriptions = $user->subscriptions()->active()->get();

        foreach ($subscriptions as $subscription) {
            $subscription->cancelNow();
        }
    }
}
