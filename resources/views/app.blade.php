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
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Scripts -->
        @routes
        <script src="{{ mix('js/app.js') }}" defer></script>
        @inertiaHead
        @paddleJS
    </head>
    <body class="font-sans antialiased bg-gray-50">
        @inertia

        <footer class="text-xs text-center mb-8">
          <div class="mb-2">Monica. All rights reserved. 2017 - {{ Carbon\Carbon::now()->year }}. Made from all over the world. We ❤️ you.</div>
          <div>This site is open source. You can read <a href="https://github.com/monicahq/customers" class="underline">the code here</a>.</div>
        </footer>

        @env ('local')
            <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script>
        @endenv
    </body>
</html>
