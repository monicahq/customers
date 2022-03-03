<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title inertia>{{ config('app.name', 'Laravel') }}</title>

  <!-- Styles -->
  <link rel="stylesheet" href="{{ mix('css/app.css') }}">

  <!-- Scripts -->
  @routes
  <script src="{{ mix('js/app.js') }}" defer></script>

  @paddleJS
</head>

<body class="font-sans antialiased bg-gray-50">
  @inertia

  @env ('local')
  <script src="http://localhost:8080/js/bundle.js"></script>
  @endenv
</body>

</html>
