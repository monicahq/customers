<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UserToken;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\One\User as OAuth1User;
use Laravel\Socialite\Two\User as OAuth2User;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class SocialiteCallbackController extends Controller
{
    /**
     * Handle socalite login.
     *
     * @param Request $request
     * @param string $driver
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function login(Request $request, string $driver): SymfonyRedirectResponse
    {
        $this->checkProvider($driver);

        $redirect = $request->input('redirect');
        if ($redirect && Str::of($redirect)->startsWith($request->getSchemeAndHttpHost())) {
            Redirect::setIntendedUrl($redirect);
        }

        return Socialite::driver($driver)->redirect();
    }

    /**
     * Handle socalite callback.
     *
     * @param Request $request
     * @param string $driver
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request, string $driver): RedirectResponse
    {
        try {
            if ($request->input('error') != '') {
                throw ValidationException::withMessages([
                    $request->input('error_description'),
                ]);
            }

            $this->checkProvider($driver);

            $provider = Socialite::driver($driver);
            if (App::environment('local')) {
                $provider->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
            }

            $user = $this->authenticateUser($driver, $provider->user());

            Auth::login($user, true);

            return Redirect::intended(route('dashboard'));
        } catch (ValidationException $e) {
            throw $e->redirectTo(Redirect::intended(route('home'))->getTargetUrl());
        }
    }

    /**
     * Authenticate the user.
     *
     * @param string $driver
     * @param \Laravel\Socialite\Contracts\User  $socialite
     *
     * @return User
     */
    private function authenticateUser(string $driver, $socialite): User
    {
        if ($userToken = UserToken::where([
            'driver_id' => $driverId = $socialite->getId(),
            'driver' => $driver,
        ])->first()) {
            // Association already exist

            $user = $userToken->user;

            if (($userId = Auth::id()) && $userId !== $user->id) {
                throw ValidationException::withMessages([
                    trans('auth.provider_already_used'),
                ]);
            }
        } else {
            // New association: create user or add token to existing user
            $user = tap($this->getUser($socialite), function ($user) use ($driver, $driverId, $socialite) {
                $this->createUserToken($user, $driver, $driverId, $socialite);
            });
        }

        return $user;
    }

    /**
     * Get authenticated user.
     *
     * @param SocialiteUser $socialite
     * @return User
     */
    private function getUser(SocialiteUser $socialite): User
    {
        if ($user = Auth::user()) {
            return $user;
        }

        // User doesn't exist
        $data = [
            'email' => $socialite->getEmail(),
            'name' => $socialite->getName() ?? $socialite->getEmail(),
            'terms' => true,
        ];

        event(new Registered($user = app(CreateNewUser::class)->create($data)));

        return $user;
    }

    /**
     * Create the user token register.
     *
     * @param User $user
     * @param string $driver
     * @param string $driverId
     * @param SocialiteUser $socialite
     * @return UserToken
     */
    private function createUserToken(User $user, string $driver, string $driverId, SocialiteUser $socialite): UserToken
    {
        $token = [
            'driver' => $driver,
            'driver_id' => $driverId,
            'user_id' => $user->id,
            'email' => $socialite->getEmail(),
        ];

        if ($socialite instanceof OAuth1User) {
            $token['token'] = $socialite->token;
            $token['token_secret'] = $socialite->tokenSecret;
            $token['format'] = 'oauth1';
        } elseif ($socialite instanceof OAuth2User) {
            $token['token'] = $socialite->token;
            $token['refresh_token'] = $socialite->refreshToken;
            $token['expires_in'] = $socialite->expiresIn;
            $token['format'] = 'oauth2';
        } else {
            throw new \Exception('authentication format not supported');
        }

        return UserToken::create($token);
    }

    /**
     * Check if the driver is activated.
     *
     * @param string $driver
     */
    private function checkProvider(string $driver): void
    {
        if (! collect(config('auth.login_providers'))->contains($driver)) {
            throw ValidationException::withMessages(['This provider does not exist.']);
        }
    }
}
