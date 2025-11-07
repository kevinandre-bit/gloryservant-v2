
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
        <meta name="viewport" content="width=device-width" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/assets/images/img/favicon-16x162.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/assets/images/img/favicon-32x322.png') }}">

        @yield('meta')

        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendor/semantic-ui/semantic.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendor/DataTables/datatables.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendor/flag-icon-css/css/flag-icon.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/style.css') }}">
        
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="{{ asset('/assets/vendor/html5shiv/html5shiv.min.js') }}></script>
            <script src="{{ asset('/assets/vendor/respond/respond.min.js') }}"></script>
        <![endif]-->
<!-- jQuery (must be loaded first) -->

        @yield('styles')
    </head>
    <body>

        <div class="wrapper">
        
        <nav id="sidebar" class="active">
  <div class="sidebar-header bg-lightblue">
    <div class="logo">
      <a href="/" class="simple-text">
        <img src="/assets/images/img/logo4.png">
      </a>
    </div>
  </div>

  <ul class="list-unstyled components" id="dynamic-menu">
    <!-- Menu links will be injected here -->
  </ul>
</nav>

        <div id="body" class="active">
            <nav class="navbar navbar-expand-lg navbar-light bg-lightblue">
                <div class="container-fluid">

                    <button type="button" id="slidesidebar" class="ui icon button btn-light-outline">
                        <i class="ui icon bars"></i> <span class="toggle-sidebar-menu">{{ __('Menu') }}</span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="list-unstyled components" id="dynamic-menu">
                          <!-- Links will be injected here -->
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="content">
                @yield('content')
            </div>

            <input type="hidden" id="_url" value="{{url('/')}}">
        </div>
    </div>

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
<script>
  const menuContainer = document.getElementById("dynamic-menu");

  Object.keys(faqData).forEach((sectionTitle, index) => {
    const anchor = `section-${index}`;
    
    // Create the list item
    const li = document.createElement("li");
    li.innerHTML = `
      <a href="#${anchor}">
        <i class="ui icon angle right"></i>
        <p>${sectionTitle}</p>
      </a>
    `;
    
    menuContainer.appendChild(li);
  });
</script>
    <script src="{{ asset('/assets/vendor/jquery/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/semantic-ui/semantic.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('/assets/js/script.js') }}"></script>

</html>