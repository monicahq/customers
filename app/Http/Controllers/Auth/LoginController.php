<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    /**
     * Display the login view.
     *
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function __invoke(Request $request): Response
    {
        /** @var \Illuminate\Support\Collection $providers */
        $providers = config('auth.login_providers');
        $providersName = $providers->mapWithKeys(function ($provider) {
            return [$provider => config("services.$provider.name") ?? __("auth.login_provider_{$provider}")];
        });

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'providers' => $providers,
            'providersName' => $providersName,
        ]);
    }
}
