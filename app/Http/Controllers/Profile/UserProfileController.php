<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\Request;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController as BaseController;
use Laravel\Jetstream\Jetstream;

class UserProfileController extends BaseController
{
    /**
     * Show the general profile settings screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function show(Request $request)
    {
        $this->validateTwoFactorAuthenticationState($request);

        /** @var Collection $providers */
        $providers = config('auth.login_providers');
        $providersName = [];
        foreach ($providers as $provider) {
            if ($name = config("services.$provider.name")) {
                $providersName[$provider] = $name;
            } else {
                $providersName[$provider] = __("auth.login_provider_{$provider}");
            }
        }

        return Jetstream::inertia()->render($request, 'Profile/Show', [
            'confirmsTwoFactorAuthentication' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
            'sessions' => $this->sessions($request)->all(),
            'providers' => $providers,
            'providersName' => $providersName,
            'userTokens' => $request->user()->usertokens()->get(),
        ]);
    }
}
