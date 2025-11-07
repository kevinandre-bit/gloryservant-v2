<!doctype html>
@php use App\Classes\permission; @endphp
@php
    // Fallback so the partial never breaks if the variable wasn't provided
    $userGeneric = $userGeneric ?? (Auth::check() ? Auth::user() : null);
@endphp
@php
  // Safe defaults so the page renders even if controller passes nothing
  $lastBatch = $lastBatch ?? null;
  $reports   = $reports   ?? (object)['technician'=>'—','operator'=>'—','admin'=>'—'];
  $onCall    = $onCall    ?? (object)['primary'=>'—','backup'=>'—'];
  $checkins  = $checkins  ?? (object)['received'=>0,'total'=>0];
  $source    = $source    ?? (object)['last_drop'=>'—'];
  $coverage  = $coverage  ?? (object)['assigned'=>0,'total'=>10];
  $critical  = $critical  ?? [];
@endphp


<html lang="{{ app()->getLocale() }}" data-bs-theme="Light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="user-id" content="{{ optional(auth()->user())->id }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Glory Servant - Radio Dashboard</title>
  <!--favicon-->
  <link rel="icon" href="/assets2/images/logo-icon.png" type="image/png">
  <!-- loader-->
  <link href="/assets2/css/pace.min.css" rel="stylesheet">
  <script src="/assets2/js/pace.min.js"></script>

  <!--plugins-->
  <link href="/assets2/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="/assets2/plugins/metismenu/metisMenu.min.css">
  <link rel="stylesheet" type="text/css" href="/assets2/plugins/metismenu/mm-vertical.css">
  <link rel="stylesheet" type="text/css" href="/assets2/plugins/simplebar/css/simplebar.css">
  <!--bootstrap css-->
  <link href="/assets2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
  <link href="/assets2/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets2/css/extra-icons.css">
  <link href="/assets2/plugins/bs-stepper/css/bs-stepper.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets2/plugins/notifications/css/lobibox.min.css">
  <link href="/assets2/plugins/input-tags/css/tagsinput.css" rel="stylesheet">
  <!--main css-->
  <link href="/assets2/css/bootstrap-extended.css" rel="stylesheet">
  <link href="/sass/main.css" rel="stylesheet">
  <link href="/sass/dark-theme.css" rel="stylesheet">
  <link href="/sass/blue-theme.css" rel="stylesheet">
  <link href="/sass/semi-dark.css" rel="stylesheet">
  <link href="/sass/bordered-theme.css" rel="stylesheet">
  <link href="/sass/responsive.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets2/css/extra-icons.css">

  <style nonce="{{ $cspNonce ?? '' }}">
    .online-indicator {
      display: inline-block;
      width: 12px;
      height: 12px;
      margin-left: 6px;
      border-radius: 50%;
      background-color: #4ac96c; /* Bootstrap’s “success” green */
      vertical-align: middle;
    }
    .offline-indicator {
      display: inline-block;
      width: 12px;
      height: 12px;
      margin-left: 6px;
      border-radius: 50%;
      background-color: gray; /* Bootstrap’s “success” green */
      vertical-align: middle;
    }
  </style>
</head>

<body class="toggled radio-ui">
   
 @yield('content')

  {{-- where your modals live --}}
  @stack('modals') {{-- if you’re using a separate stack for modals, otherwise just ensure the modal is in the page --}}

  {{-- Bootstrap JS --}}
  <script src="/js/bootstrap.bundle.min.js"></script>

  {{-- your pushed scripts --}}
  @stack('scripts')
 <!--start header-->
  @include ('admin/nav')
  <!--end top header-->

    <!--start sidebar-->
  @include ('admin/radio_sidebar')
  <!--end top sidebar-->
    <!--bootstrap js-->


<!--start overlay-->
     <div class="overlay btn-toggle"></div>
  <!--end overlay-->

   <!--start footer-->
   <footer class="page-footer">
    <p class="mb-0">Copyright © 2025. All right reserved.</p>
  </footer>
  <!--end footer-->




  <!--start switcher-->
  <button class="btn btn-grd btn-grd-primary position-fixed bottom-0 end-0 m-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop">
    <i class="material-icons-outlined">tune</i>Customize
  </button>
  
  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="staticBackdrop">
    <div class="offcanvas-header border-bottom h-70">
      <div class="">
        <h5 class="mb-0">Theme Customizer</h5>
        <p class="mb-0">Customize your theme</p>
      </div>
      <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
        <i class="material-icons-outlined">close</i>
      </a>
    </div>
    <div class="offcanvas-body">
      <div>
        <p>Theme variation</p>

        <div class="row g-3">
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BlueTheme" checked>
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BlueTheme">
              <span class="material-icons-outlined">contactless</span>
              <span>Blue</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="LightTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="LightTheme">
              <span class="material-icons-outlined">light_mode</span>
              <span>Light</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="DarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="DarkTheme">
              <span class="material-icons-outlined">dark_mode</span>
              <span>Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="SemiDarkTheme" >
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="SemiDarkTheme">
              <span class="material-icons-outlined">contrast</span>
              <span>Semi Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BoderedTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BoderedTheme">
              <span class="material-icons-outlined">border_style</span>
              <span>Bordered</span>
            </label>
          </div>


        </div><!--end row-->

      </div>
    </div>
  </div>
     <!--start switcher-->
<script>
  @if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
      Lobibox.notify('success', {
        pauseDelayOnHover: true,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        icon: 'bi bi-check2-circle',
        msg: @json(session('success'))
      });
    });
  @endif

  @if(session('error'))
    document.addEventListener('DOMContentLoaded', function() {
      Lobibox.notify('error', {
        pauseDelayOnHover: true,
    continueDelayOnInactiveTab: false,
    position: 'top right',
    icon: 'bi bi-x-circle',
        msg: @json(session('error'))
      });
    });
  @endif
</script>
  <script src="/assets2/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.7/minified/html5-qrcode.min.js"></script>

<!--plugins-->
<script src="/assets2/js/jquery.min.js"></script>
<script src="/assets2/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
<script src="/assets2/plugins/metismenu/metisMenu.min.js"></script>
<script src="/assets2/plugins/simplebar/js/simplebar.min.js"></script>
<script src="/assets2/plugins/peity/jquery.peity.min.js"></script>
<script>
  $(".data-attributes span").peity("donut")
</script>
<script src="/assets2/js/main.js"></script>
<script src="/assets2/js/dashboard1.js"></script>
<script>
   new PerfectScrollbar(".user-list")
</script>

<script src="{{ asset('assets3/js/feather.min.js') }}"></script>
<script nonce="{{ $cspNonce ?? '' }}">
  feather.replace()
</script>

<script src="/assets2/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="/assets2/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="/assets2/plugins/notifications/js/lobibox.min.js"></script>
<script src="/assets2/plugins/notifications/js/notifications.min.js"></script>
<script src="/assets2/plugins/notifications/js/notification-custom-script.js"></script>
<script src="/assets2/plugins/input-tags/js/tagsinput.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="/assets2/plugins/bs-stepper/js/bs-stepper.min.js"></script>
<script src="/assets2/plugins/bs-stepper/js/main.js"></script>
{{-- resources/views/layouts/new_layout.blade.php --}}

{{-- ... your normal layout ... --}}

{{-- Permission Denied modal (markup always present but hidden) --}}
<div class="modal fade" id="deniedModal" tabindex="-1" aria-labelledby="deniedLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bx bx-lock-alt me-1"></i> Permission Denied</h5>
      </div>
      <div class="modal-body">
        <p>{{ session('denied') ?? 'Sorry, you don’t have permission to access this feature.' }}</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Go Back</button>
        <a href="{{ url('/') }}" class="btn btn-primary">Home</a>
      </div>
    </div>
  </div>
</div>

{{-- Only trigger it when the flash exists --}}
@if(session('denied'))
  <script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function () {
      const m = new bootstrap.Modal(document.getElementById('deniedModal'));
      m.show();
    });
  </script>
@endif
  <script nonce="{{ $cspNonce ?? '' }}">
    
    $(".datepicker").flatpickr();

    $(".time-picker").flatpickr({
      enableTime: true,
      noCalendar: true,
      dateFormat: "G:i K",   // e.g. “9:00 AM”
      time_24hr: false
    });
    $(".date-time").flatpickr({
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });

    $(".date-format").flatpickr({
      altInput: true,
      altFormat: "F j, Y",
      dateFormat: "Y-m-d",
    });

    $(".date-range").flatpickr({
      mode: "range",
      altInput: true,
      altFormat: "F j, Y",
      dateFormat: "Y-m-d",
    });

    $(".date-inline").flatpickr({
      inline: true,
      altInput: true,
      altFormat: "F j, Y",
      dateFormat: "Y-m-d",
    });
  </script>
<script nonce="{{ $cspNonce ?? '' }}">
(function(){
  function ping() {
    fetch("{{ route('session.ping') }}", { credentials: 'include' })
      .then(r => {
        if (r.status === 401) {
          window.location.href = "{{ route('login') }}";
        }
      })
      .catch(()=>{});
  }
  setInterval(ping, 60_000);
})();
</script>


  @stack('scripts')

</body>
