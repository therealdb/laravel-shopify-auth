<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Redirecting...</title>

        <script type="text/javascript">
            @if (isset($url))
            window.top.location.href = "{{ $url }}"
            @else
            window.top.location.href = "{{ route('shopify.login') }}"
            @endif
        </script>
        @stack('styles')
        
    </head>
    <body>
    </body>
</html>
