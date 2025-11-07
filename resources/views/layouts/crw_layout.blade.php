<!doctype html>
@php use App\Classes\permission; @endphp
@php
    // Fallbacks so partials never break if controller didn't pass them
    $userGeneric = $userGeneric ?? (Auth::check() ? Auth::user() : null);

    $lastBatch = $lastBatch ?? null;
    $reports   = $reports   ?? (object)['technician'=>'—','operator'=>'—','admin'=>'—'];
    $onCall    = $onCall    ?? (object)['primary'=>'—','backup'=>'—'];
    $checkins  = $checkins  ?? (object)['received'=>0,'total'=>0];
    $source    = $source    ?? (object)['last_drop'=>'—'];
    $coverage  = $coverage  ?? (object)['assigned'=>0,'total'=>10];
    $critical  = $critical  ?? [];
@endphp
<html lang="{{ app()->getLocale() }}" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="user-id" content="{{ optional(auth()->user())->id }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Glory Servant - Volunteer Management syste</title>

  <!--favicon-->
  <link rel="icon" href="/assets2/images/logo-icon.png" type="image/png">

  <!-- loader -->
  <link href="/assets2/css/pace.min.css" rel="stylesheet">
  <script src="/assets2/js/pace.min.js"></script>

  <!-- Core CSS -->
  <link href="/assets2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">

  <!-- Plugins CSS -->
  <link href="/assets2/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="/assets2/plugins/metismenu/metisMenu.min.css">
  <link rel="stylesheet" type="text/css" href="/assets2/plugins/metismenu/mm-vertical.css">
  <link rel="stylesheet" type="text/css" href="/assets2/plugins/simplebar/css/simplebar.css">
  <link href="/assets2/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
  <link href="/assets2/plugins/bs-stepper/css/bs-stepper.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets2/plugins/notifications/css/lobibox.min.css">
  <link href="/assets2/plugins/input-tags/css/tagsinput.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets2/css/extra-icons.css">

  <!-- App CSS -->
  <link href="/assets2/css/bootstrap-extended.css" rel="stylesheet">
  <link href="/sass/main.css" rel="stylesheet">
  <link href="/sass/dark-theme.css" rel="stylesheet">
  <link href="/sass/blue-theme.css" rel="stylesheet">
  <link href="/sass/semi-dark.css" rel="stylesheet">
  <link href="/sass/bordered-theme.css" rel="stylesheet">
  <link href="/sass/responsive.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets2/css/extra-icons.css">

  {{-- Optional: allow pages that still use @section("scripts") in <head> (kept; not recommended) --}}
  @yield('head-scripts')
</head>

<body class="toggled">

  <!--start header-->
  @include ('admin/nav')
  <!--end top header-->

  <!--start sidebar-->
  @include ('admin/crw_sidebar')
  <!--end top sidebar-->

  {{-- Main slot --}}
  @yield('content')

  {{-- Where your modals live (if you push to this stack) --}}
  @stack('modals')



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
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <div>
        <p>Theme variation</p>

        <div class="row g-3">
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BlueTheme" >
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
            <input type="radio" class="btn-check" name="theme-options" id="DarkTheme" checked>
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
            <input type="radio" class="btn-check" name="theme-options" id="BoderedTheme" >
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BoderedTheme">
              <span class="material-icons-outlined">border_style</span>
              <span>Bordered</span>
            </label>
          </div>


        </div><!--end row-->

      </div>
    </div>
  </div>
  {{-- ===== Core & Vendor JS (ORDER MATTERS) ===== --}}

  {{-- jQuery FIRST (DataTables depends on it) --}}
  <script src="/assets2/js/jquery.min.js"></script>

  {{-- Bootstrap (single, valid path) --}}
  <script src="/assets2/js/bootstrap.bundle.min.js"></script>

  {{-- Vendor Plugins --}}
  <script src="/assets2/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  <script src="/assets2/plugins/metismenu/metisMenu.min.js"></script>
  <script src="/assets2/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="/assets2/plugins/peity/jquery.peity.min.js"></script>

  {{-- App Core --}}
  <script src="/assets2/js/main.js"></script>
  {{-- REMOVE demo auto-charts: dashboard1.js caused “Element not found” errors --}}
  {{-- <script src="/assets2/js/dashboard1.js"></script> --}}

  <meta name="session-ping-url" content="{{ route('session.ping') }}">
  <meta name="login-url" content="{{ route('login') }}">

  {{-- Icons (self-hosted) --}}
  <script src="{{ asset('assets3/js/feather.min.js') }}"></script>

  {{-- Tables & Charts (DataTables base; chart libs should be loaded by pages that need them) --}}
  <script src="{{ asset('assets3/js/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets3/js/dataTables.bootstrap5.min.js') }}"></script>

  {{-- Notifications --}}
  <script src="/assets2/plugins/notifications/js/lobibox.min.js"></script>
  <script src="/assets2/plugins/notifications/js/notifications.min.js"></script>
  <script src="/assets2/plugins/notifications/js/notification-custom-script.js"></script>

  {{-- Input helpers --}}
  <script src="/assets2/plugins/input-tags/js/tagsinput.js"></script>
  @if (config('assets.use_cdn'))
    <script src="{{ config('assets.cdn.flatpickr_js') }}" defer crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  @else
    <script src="{{ asset(config('assets.local.flatpickr_js')) }}" defer></script>
  @endif
  <script src="/assets2/plugins/bs-stepper/js/bs-stepper.min.js"></script>
  {{-- Remove unguarded demo init; guard it --}}


  {{-- QR scanner --}}
  @if (config('assets.use_cdn'))
    <script src="{{ config('assets.cdn.html5_qrcode') }}" defer crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  @else
    <script src="{{ asset(config('assets.local.html5_qrcode')) }}" defer></script>
  @endif

  <div id="flash-data" hidden data-success="{{ session('success') }}" data-error="{{ session('error') }}"></div>

  <div id="denied-flag" hidden data-show="{{ session('denied') ? 1 : 0 }}"></div>


  <script src="{{ asset('assets3/js/layouts-new.js') }}" defer></script>

  {{-- Permission Denied modal markup (kept) --}}
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
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <a href="{{ url('/') }}" class="btn btn-primary">Home</a>
        </div>
      </div>
    </div>
  </div>

  {{-- denied handled by external script --}}

  {{-- ===== Page level hooks ===== --}}
  @yield('scripts')
  @stack('scripts')
  @stack('page-scripts')

  {{-- People chooser config & script --}}
  <script id="people-config" type="application/json">{"people": @json($peopleArray ?? []), "preselected": @json($memberIds ?? [])}</script>
  <script src="{{ asset('assets3/js/people-chooser.js') }}" defer></script>
  {{-- The legacy inline bootstrapper is removed; people-chooser.js now initializes from #people-config --}}

</body> 
</html>
