<?php

namespace App\Exceptions;

use Illuminate\Support\Str;
use Sentry\Tracing\SamplingContext;

class Sentry
{
    public static function tracesSampler(SamplingContext $context): float
    {
        $url = optional($context->getTransactionContext())->getData()['url'];
        if ($url === '/sentry' || Str::startsWith($url, '/_debugbar')) {
            return 0.0;
        }

        return config('sentry.traces_sample_rate');
    }
}
