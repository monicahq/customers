<?php

namespace App\Providers;

use App\Models\Receipt;
use App\Models\Subscription;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Paddle\Cashier;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\MarkdownConverter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Str::macro('markdownExternalLink', function (string $string, string $class = 'underline'): string {
            $config = [
                'external_link' => [
                    'internal_hosts' => config('app.url'),
                    'open_in_new_window' => false,
                    'html_class' => $class,
                    'nofollow' => '',
                    'noopener' => 'external',
                    'noreferrer' => 'external',
                ]
            ];
            $environment = new Environment($config);
            $environment->addExtension(new CommonMarkCoreExtension());
            $environment->addExtension(new GithubFlavoredMarkdownExtension());
            $environment->addExtension(new ExternalLinkExtension());

            $converter = new MarkdownConverter($environment);

            $result = (string) $converter->convert($string);
            $result = ltrim($result, '<p>');
            $result = rtrim($result, '</p>');

            return $result;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Cashier::useSubscriptionModel(Subscription::class);
        Cashier::useReceiptModel(Receipt::class);

        RateLimiter::for('oauth2-socialite', function (Request $request) {
            return Limit::perMinute(5)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
