@php
    use Illuminate\Support\Str;
    $route = Route::currentRouteName() ?? '';
    // helpers
    $is = fn ($names) => Str::is((array) $names, $route);
    $in = fn ($prefix) => Str::startsWith($route, $prefix);
@endphp

<!-- Favicon / Icons -->
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets3/img/favicon.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets3/img/apple-touch-icon.png') }}">

<!-- Core CSS (RTL aware if you have an rtl route) -->
@if($is('src.layout-rtl'))
  <link rel="stylesheet" href="{{ asset('assets3/css/bootstrap.rtl.min.css') }}">
@else
  <link rel="stylesheet" href="{{ asset('assets3/css/bootstrap.min.css') }}">
@endif

<!-- Vendor icon packs (load what you actually use globally) -->
<link rel="stylesheet" href="{{ asset('assets3/plugins/icons/feather/feather.css') }}">
<link rel="stylesheet" href="{{ asset('assets3/plugins/tabler-icons/tabler-icons.css') }}">
<link rel="stylesheet" href="{{ asset('assets3/plugins/fontawesome/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets3/plugins/fontawesome/css/all.min.css') }}">

{{-- Optional libs: only load globally if most pages need them --}}
@if(!$in('auth.') && !$in('errors.') && !$in('landing.'))
  <link rel="stylesheet" href="{{ asset('assets3/plugins/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets3/plugins/flatpickr/flatpickr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets3/plugins/@simonwep/pickr/themes/nano.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets3/plugins/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets3/css/dataTables.bootstrap5.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets3/plugins/summernote/summernote-lite.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets3/css/bootstrap-datetimepicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets3/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
@endif

{{-- Page/feature specific (route-based) --}}
@switch($route)
  @case('src.maps-leaflet')
      <link rel="stylesheet" href="{{ asset('assets3/plugins/leaflet/leaflet.css') }}">
      @break
  @case('src.maps-vector')
      <link rel="stylesheet" href="{{ asset('assets3/plugins/jsvectormap/css/jsvectormap.min.css') }}">
      @break
  @case('src.ui-scrollbar')
      <link rel="stylesheet" href="{{ asset('assets3/plugins/scrollbar/scroll.min.css') }}">
      @break
  @case('src.ui-stickynote')
      <link rel="stylesheet" href="{{ asset('assets3/plugins/stickynote/sticky.css') }}">
      @break
  @case('src.icon-bootstrap')
      <link rel="stylesheet" href="{{ asset('assets3/plugins/icons/bootstrap/bootstrap-icons.min.css') }}">
      @break
  @case('src.icon-remix')
      <link rel="stylesheet" href="{{ asset('assets3/plugins/remix/fonts/remixicon.css') }}">
      @break
  @case('src.ui-lightbox')
      <link rel="stylesheet" href="{{ asset('assets3/plugins/lightbox/glightbox.min.css') }}">
      @break
  @case('src.chart-c3')
      <link rel="stylesheet" href="{{ asset('assets3/plugins/c3-chart/c3.min.css') }}">
      @break
  @case('src.ui-swiperjs')
      <link rel="stylesheet" href="{{ asset('assets3/plugins/swiper/swiper-bundle.min.css') }}">
      @break
@endswitch

{{-- Main theme CSS always last so it can override vendor styles --}}
<link rel="stylesheet" href="{{ asset('assets3/css/style.css') }}">

{{-- Allow views to inject extra CSS without editing this partial --}}
@stack('styles')
