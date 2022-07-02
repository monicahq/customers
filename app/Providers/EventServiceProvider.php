<?php

namespace App\Providers;

use App\Listeners\LocaleUpdatedListener;
use App\Listeners\LoginListener;
use App\Listeners\SubscriptionCancelledListener;
use App\Listeners\SubscriptionCreatedListener;
use App\Listeners\SubscriptionPaymentSucceededListener;
use App\Providers\Auth\MonicaExtendSocialite;
use App\Providers\Auth\OfficeLifeExtendSocialite;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Laravel\Paddle\Events\SubscriptionCancelled;
use Laravel\Paddle\Events\SubscriptionCreated;
use Laravel\Paddle\Events\SubscriptionPaymentSucceeded;
use SocialiteProviders\GitHub\GitHubExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Login::class => [
            LoginListener::class,
        ],
        LocaleUpdated::class => [
            LocaleUpdatedListener::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SocialiteWasCalled::class => [
            GitHubExtendSocialite::class,
            MonicaExtendSocialite::class,
            OfficeLifeExtendSocialite::class,
        ],
        SubscriptionCancelled::class => [
            SubscriptionCancelledListener::class,
        ],
        SubscriptionCreated::class => [
            SubscriptionCreatedListener::class,
        ],
        SubscriptionPaymentSucceeded::class => [
            SubscriptionPaymentSucceededListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
