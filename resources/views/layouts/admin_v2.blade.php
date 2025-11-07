<!doctype html>
@php use App\Classes\permission; @endphp
@php
    // Fallback so the partial never breaks if the variable wasn't provided
    $userGeneric = $userGeneric ?? (Auth::check() ? Auth::user() : null);
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
  <link href="/sass/semi-dark.css" rel="stylesheet">
  <link href="/sass/bordered-theme.css" rel="stylesheet">
  <link href="/sass/responsive.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets2/css/extra-icons.css">

  <link rel="stylesheet" href="/assets3/new_layout2.css">
</head>

<body>
  <div id="flash-data" hidden
       data-success="{{ session('success') }}"
       data-info="{{ session('info') }}"
       data-error="{{ session('error') }}"
       data-errors='@json($errors->any() ? $errors->all() : [])'>
  </div>

  @yield('content')

  {{-- where your modals live --}}
  @stack('modals') {{-- if you’re using a separate stack for modals, otherwise just ensure the modal is in the page --}}

  {{-- Bootstrap JS --}}



  
<script src="/assets3/new_layout2.js" defer></script>
  <script src="/assets2/js/bootstrap.bundle.min.js"></script>
@if (config('assets.use_cdn'))
  <script src="{{ config('assets.cdn.html5_qrcode') }}" defer crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@else
  <script src="{{ asset(config('assets.local.html5_qrcode')) }}" defer></script>
@endif

<!--plugins-->
<script src="/assets2/js/jquery.min.js"></script>
<script src="/assets2/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
<script src="/assets2/plugins/metismenu/metisMenu.min.js"></script>
<script src="/assets2/plugins/apexchart/apexcharts.min.js"></script>
<script src="/assets2/plugins/simplebar/js/simplebar.min.js"></script>
<script src="/assets2/plugins/peity/jquery.peity.min.js"></script>
<script src="/assets2/js/main.js"></script>
<script src="/assets2/js/dashboard1.js"></script>
<!-- init moved to /assets3/new_layout2.js -->

<script src="{{ asset('assets3/js/feather.min.js') }}"></script>
<!-- init moved to /assets3/new_layout2.js -->

<script src="{{ asset('assets3/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets3/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="/assets2/plugins/notifications/js/lobibox.min.js"></script>
<script src="/assets2/plugins/notifications/js/notifications.min.js"></script>
<script src="/assets2/plugins/notifications/js/notification-custom-script.js"></script>
<script src="/assets2/plugins/input-tags/js/tagsinput.js"></script>
@if (config('assets.use_cdn'))
  <script src="{{ config('assets.cdn.flatpickr_js') }}" defer crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@else
  <script src="{{ asset(config('assets.local.flatpickr_js')) }}" defer></script>
@endif
<script src="/assets2/plugins/bs-stepper/js/bs-stepper.min.js"></script>
<script src="/assets2/plugins/bs-stepper/js/main.js"></script>
<!-- flatpickr init moved to /assets3/new_layout2.js -->
<!-- ajax error handler moved to /assets3/new_layout2.js -->
