<?php

namespace App\Exceptions;

use Illuminate\Support\Str;
use Sentry\Tracing\SamplingContext;

class Sentry
{
    /**
     * Sentry trace sampler analyzer.
     *
     * @param  \Sentry\Tracing\SamplingContext  $context
     * @return float
     *
     * @codeCoverageIgnore
     */
    public static function tracesSampler(SamplingContext $context): float
    {
        $url = optional($context->getTransactionContext())->getData()['url'];
        if (Str::startsWith($url, '/sentry') || Str::startsWith($url, '/_debugbar')) {
            return 0.0;
        }

        return config('sentry.traces_sample_rate');
    }
}
