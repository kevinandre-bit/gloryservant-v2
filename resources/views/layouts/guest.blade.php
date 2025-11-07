
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
        <meta name="viewport" content="width=device-width" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/assets/images/img/favicon-16x162.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/assets/images/img/favicon-32x322.png') }}">
        
<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('assets3/plugins/icons/bootstrap/bootstrap-icons.min.css') }}">
<link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/components/faqs/faq-3/assets/css/faq-3.css">

        @yield('head')

    </head>
    <body>

        <div class="wrapper">
        

        <div id="body" class="active">

            <div class="content">
                @yield('content')
            </div>

            <input type="hidden" id="_url" value="{{url('/')}}">
            <script>
                var y = '@isset($var){{$var}}@endisset';
            </script>
        </div>
    </div>
<script src="https://unpkg.com/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
       

<!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/6808a0d064953a190caf63d4/1ipgr8c6g';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--<script src="https://cdn.botpress.cloud/webchat/v2.4/inject.js"></script>
<script src="https://files.bpcontent.cloud/2025/04/25/22/20250425222739-FUTQR243.js"></script>-->
    </body>
</html>
