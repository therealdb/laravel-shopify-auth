<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ env('APP_NAME', 'Laravel Shopify Auth') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="/vendor/shopify-auth/css/shopify-auth.css">
        @stack('styles')
        
    </head>
    <body>
        @yield('content')
        <script src="/vendor/shopify-auth/js/shopify-auth.js"></script>
        @stack('scripts')
    </body>
</html>
