<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title inertia>{{ config('app.name', 'Laravel') }}</title>

  <link rel="shortcut icon" href="https://www.monicahq.com/img/favicon.png">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">

  <!-- Scripts -->
  @routes
  <script src="{{ mix('js/app.js') }}" defer></script>

  @paddleJS
</head>

<body class="font-sans antialiased bg-gray-50">
  @inertia

  <footer class="text-xs text-center mb-8">
    <div class="mb-2">Monica. All rights reserved. 2017 - {{ Carbon\Carbon::now()->year }}. Made from all over the world. We ❤️ you.</div>
    <div>This site is open source. You can read <a href="https://github.com/monicahq/customers" class="underline">the code here</a>.</div>
  </footer>

  @env ('local')
  <script src="http://localhost:8080/js/bundle.js"></script>
  @endenv
</body>

</html>
