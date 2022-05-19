<?php

namespace App\Actions\Jetstream;

use App\Services\DestroyAccount;
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
        app(DestroyAccount::class)->execute([
            'user_id' => $user->id,
        ]);
    }
}
