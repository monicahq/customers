<?php

namespace App\Providers;

use App\Actions\AttemptToAuthenticateWebauthn;
use App\Models\Receipt;
use App\Models\Subscription;
use App\Http\Responses\LoginViewResponse;
use App\Http\Responses\RegisterViewResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Paddle\Cashier;
use LaravelWebauthn\Actions\PrepareAuthenticatedSession;
use LaravelWebauthn\Services\Webauthn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Webauthn::loginViewResponseUsing(LoginViewResponse::class);
        Webauthn::registerViewResponseUsing(RegisterViewResponse::class);

        Cashier::useSubscriptionModel(Subscription::class);
        Cashier::useReceiptModel(Receipt::class);

        RateLimiter::for('oauth2-socialite', function (Request $request) {
            return Limit::perMinute(5)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
