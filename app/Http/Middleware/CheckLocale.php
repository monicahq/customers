<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Vluzrmos\LanguageDetector\Contracts\LanguageDetectorInterface;

class CheckLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = $request->query('lang');

        if (empty($locale)) {
            $locale = $this->getLocale();
        }

        App::setLocale($locale);

        return $next($request);
    }

    /**
     * Get the current or default locale.
     *
     * @return string
     */
    protected function getLocale()
    {
        if (Auth::check()) {
            $locale = Auth::user()->locale;
        } else {
            $locale = app(LanguageDetectorInterface::class)->detect() ?? config('app.locale');
        }

        return $locale;
    }
}
