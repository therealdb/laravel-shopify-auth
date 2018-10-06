<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ env('APP_NAME', 'Laravel Shopify Auth') }}</title>

        <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
        <script type='text/javascript'>
        if (window.top == window.self) {
            window.location.assign('{{ $url }}');
        } else {
            ShopifyApp.redirect('{{ $url }}');
        }
        </script>
    </head>
    <body>
        @yield('content')
    </body>
</html>
