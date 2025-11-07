<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
        <meta name="viewport" content="width=device-width" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/assets/images/img/favicon-16x162.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/assets/images/img/favicon-32x322.png') }}">
        
        <title>Web Time Clock | Workday Time Clock</title>
        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendor/semantic-ui/semantic.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/clock.css') }}">

        @yield('styles')
    </head>
    <body>
    
    <img src="{{ asset('/assets/images/img/clock-background.png') }}" class="wave">
    <div class="wrapper">
        <div id="body">
            <div class="content">

                @yield('content')
             
            </div>
        </div>
    </div>

    <script src="{{ asset('/assets/vendor/jquery/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/momentjs/moment.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/momentjs/moment-timezone-with-data.js') }}"></script>
    <script src="{{ asset('/assets/vendor/semantic-ui/semantic.min.js') }}"></script>
    <script src="{{ asset('/assets/js/script.js') }}"></script>

    <script nonce="{{ $cspNonce ?? '' }}">
        var timezone = "@isset($tz){{ $tz }}@endisset";
    </script>

    @yield('scripts')

    </body>
</html>
