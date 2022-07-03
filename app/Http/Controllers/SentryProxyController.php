<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SentryProxyController extends Controller
{
    /**
     * Handle a sentry request.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function handle(Request $request)
    {
        if (app()->bound('sentry')) {
            $project_id = Str::of(config('sentry.dsn'))->trim('/')->afterLast('/');

            return Http::withBody($request->getContent(), 'application/x-sentry-envelope')
                ->post("https://sentry.io/api/$project_id/envelope/");
        }

        abort(404);
    }
}
