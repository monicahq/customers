<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdateAddressRequest;
use App\Services\UpdateAddress;

class UserAddressController extends Controller
{
    /**
     * Update user account.
     *
     * @param  \App\Http\Requests\Account\UpdateAddressRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateAddressRequest $request)
    {
        app(UpdateAddress::class)->execute($request->only([
            'address_line_1',
            'address_line_2',
            'city',
            'postal_code',
            'country',
            'state',
        ]) + [
            'user_id' => auth()->id(),
        ]);

        return back(303)->with('status', 'user-address-updated');
    }
}
