<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="shortcut icon" href="https://www.monicahq.com/img/favicon.png">

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">

        <!-- Scripts -->
        @routes
        <script src="{{ asset(mix('js/app.js')) }}" defer></script>
        @inertiaHead
        @paddleJS

        @if (app()->bound('sentry'))
        <script>
          const SentryConfig = {!! \json_encode([
            'dsn' => config('sentry.dsn'),
            'environment' => config('sentry.environment'),
            'sendDefaultPii' => config('sentry.send_default_pii'),
            'tracesSampleRate' => config('sentry.traces_sample_rate'),
          ]); !!}
        </script>
        @endif
    </head>
    <body class="font-sans antialiased bg-gray-50">
        @inertia
        @env ('local')
            <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script>
        @endenv
    </body>
</html>
