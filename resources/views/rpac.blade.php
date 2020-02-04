@auth
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user" content="{{ auth()->user() }}">

    <title>{{ config('app.name', 'RPAC') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('vendor/rpac/rpac.js') }}" defer></script>

    <!-- Styles -->
    <style type="text/css">
        body {
            font-family: Arial;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
</head>
<body>
<div id="rpac"></div>
<a id="logout-link" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" style="display: none;">
    {{ __('Logout') }}
</a>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
</body>
</html>
@endauth
