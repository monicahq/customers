<?php

namespace App\Actions;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\LoginRateLimiter;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\One\User as OAuth1User;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User as OAuth2User;

class AttemptToAuthenticateSocialite
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * The login rate limiter instance.
     *
     * @var \Laravel\Fortify\LoginRateLimiter
     */
    protected $limiter;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @param  \Laravel\Fortify\LoginRateLimiter  $limiter
     * @return void
     */
    public function __construct(StatefulGuard $guard, LoginRateLimiter $limiter)
    {
        $this->guard = $guard;
        $this->limiter = $limiter;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle(Request $request, callable $next)
    {
        $driver = $request->route()->parameter('driver');

        $provider = $this->getSocialiteProvider($driver);
        $user = $this->authenticateUser($request, $driver, $provider->user());

        if (! $user) {
            $this->fireFailedEvent($request);

            return $this->throwFailedAuthenticationException($request, $driver);
        }

        $this->guard->login($user, true /* $request->boolean('remember') */);

        return $next($request);
    }

    /**
     * Get the provider.
     *
     * @param  string  $driver
     * @return \Laravel\Socialite\Contracts\Provider
     */
    private function getSocialiteProvider(string $driver): Provider
    {
        $provider = Socialite::driver($driver);

        if (App::environment('local') && $provider instanceof AbstractProvider) {
            $provider->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
        }

        return $provider;
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $driver
     * @param  \Laravel\Socialite\Contracts\User  $socialite
     * @return User
     */
    private function authenticateUser(Request $request, string $driver, SocialiteUser $socialite): User
    {
        if ($userToken = UserToken::where([
            'driver_id' => $socialite->getId(),
            'driver' => $driver,
        ])->first()) {
            // Association already exist

            $user = $userToken->user;

            $this->checkUserAssociation($request, $user, $driver);
        } else {
            // New association: create user or add token to existing user
            $user = tap($this->getUserOrCreate($socialite), function ($user) use ($driver, $socialite) {
                $this->createUserToken($user, $driver, $socialite);
            });
        }

        return $user;
    }

    /**
     * Check if the user is logged in and if the user is the same as the one.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @param  string  $driver
     * @return void
     */
    private function checkUserAssociation(Request $request, User $user, string $driver)
    {
        if (($userId = Auth::id()) && $userId !== $user->id) {
            $this->fireFailedEvent($request, Auth::user());

            throw ValidationException::withMessages([
                $driver => [trans('auth.provider_already_used')],
            ]);
        }
    }

    /**
     * Get authenticated user.
     *
     * @param  SocialiteUser  $socialite
     * @return User
     */
    private function getUserOrCreate(SocialiteUser $socialite): User
    {
        if ($user = Auth::user()) {
            return $user;
        }

        return $this->createUser($socialite);
    }

    /**
     * Create new user.
     *
     * @param  SocialiteUser  $socialite
     * @return User
     */
    private function createUser(SocialiteUser $socialite): User
    {
        $data = [
            'email' => $socialite->getEmail(),
            'name' => empty($socialite->getName()) ? $socialite->getEmail() : $socialite->getName(),
            'terms' => true,
        ];

        event(new Registered($user = app(CreateNewUser::class)->create($data)));

        return $user;
    }

    /**
     * Create the user token register.
     *
     * @param  User  $user
     * @param  string  $driver
     * @param  SocialiteUser  $socialite
     * @return UserToken
     */
    private function createUserToken(User $user, string $driver, SocialiteUser $socialite): UserToken
    {
        $token = [
            'driver' => $driver,
            'driver_id' => $socialite->getId(),
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
     * Throw a failed authentication validation exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $driver
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function throwFailedAuthenticationException(Request $request, string $driver)
    {
        $this->limiter->increment($request);

        throw ValidationException::withMessages([
            $driver => [trans('auth.failed')],
        ]);
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User|null  $user
     * @return void
     */
    protected function fireFailedEvent(Request $request, ?User $user = null)
    {
        event(new Failed('web', $user, [
            'email' => $request->email,
        ]));
    }
}