<html lang="{{ app()->getLocale() }}">
@php use App\Classes\permission; @endphp
@php
  // Safe permission checker: returns true/false without throwing
  $can = function (?string $key) {
    if (is_null($key) || $key === '') return true; // public
    try {
      return \App\Classes\permission::permitted($key) === 'success';
    } catch (\Throwable $e) {
      // Table missing or helper error — don't crash the page
      return false;
    }
  };
@endphp
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
        <meta name="viewport" content="width=device-width" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/assets/images/img/favicon-16x162.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/assets/images/img/favicon-32x322.png') }}">
        <meta name="user-id" content="{{ auth()->user()->id }}">

    <link href="{{ asset('assets3/plugins/fontawesome/css/all.min.css') }}" rel="stylesheet">
        
        @yield('meta')

        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendor/semantic-ui/semantic.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendor/DataTables/datatables.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/vendor/flag-icon-css/css/flag-icon.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/style.css') }}">
        <link rel="stylesheet" href="/assets3/personal.css">
        
        {{-- Removed legacy IE8 shims to avoid malformed script tags and CSP noise --}}

<script src="/assets2/js/jquery.min.js"></script>
<script src="{{ asset('/assets/vendor/semantic-ui/semantic.min.js') }}"></script>
        @yield('styles')
    </head>
    <body> 

        <div class="wrapper">
        
        <nav id="sidebar" class="active">
            <div class="sidebar-header bg-lightblue">
                <div class="logo">
                <a href="/" class="simple-text">
                    <img src="{{ asset('assets3/img/logo.png') }}" style="padding-top:10px;">
                </a>
                </div>
            </div>

            <ul class="list-unstyled components">
                <li class="">
                    <a href="{{ url('personal/dashboard') }}">
                        <i class="ui icon sliders horizontal"></i>
                        <p>{{ __("Dashboard") }}</p>
                    </a>
                </li>
                <li class="">
                    <a href="{{ url('personal/attendance/view') }}">
                        <i class="ui icon clock outline"></i>
                        <p>{{ __("My Attendances") }}</p>
                    </a>
                </li>
                <li class="">
                    <a href="{{ url('personal/schedules/view') }}">
                        <i class="ui icon calendar alternate outline"></i>
                        <p>{{ __("My Schedules") }}</p>
                    </a>
                </li>
                <!--<li class="">
                        <a href="{{ url('personal/requests/view') }}">
                            <i class="ui icon info outline"></i>
                            <p>{{ __("My Requests") }}</p>
                        </a>
                </li>-->
                <li class="">
                    <a href="{{ url('personal/leaves/view') }}">
                        <i class="ui icon calendar plus outline"></i>
                        <p>{{ __("My Leave") }}</p>
                    </a>
                </li>
                <li class="">
                    <a href="{{ url('personal/devotion/view') }}">
                        <i class="icon book"></i>
                        <p>{{ __("Devotion") }}</p>
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('personal.creative.dashboard') }}">
                        <i class="ui icon paint brush"></i>
                        <p>{{ __("Creative Tasks") }}</p>
                    </a>
                </li>
                <li>
                    <a href="{{ url('personal/settings') }}">
                        <i class="icon file"></i>
                        <p>{{ __("Make a Request") }}</p>
                    </a>
                </li>
            </ul>
        </nav>

        <div id="body" class="active">
            <nav class="navbar navbar-expand-lg navbar-light bg-lightblue">
                <div class="container-fluid">

                    <button type="button" id="slidesidebar" class="ui icon button btn-light-outline">
                        <i class="ui icon bars"></i> <span class="toggle-sidebar-menu">{{ __('Menu') }}</span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="nav navbar-nav ml-auto navmenu">
                            <li class="nav-item">
                                <div class="ui pointing link dropdown item" tabindex="0">
                                    <i class="ui icon flag"></i> <span class="navmenutext uppercase">{{ env('APP_LOCALE', 'en') }}</span>
                                    <i class="dropdown icon"></i>
                                    <div class="menu" tabindex="-1">
                                      <a href="{{ url('lang/en') }}" class="item"><i class="flag-icon flag-icon-us"></i>English</a>
                                      <a href="{{ url('lang/es') }}" class="item"><i class="flag-icon flag-icon-es"></i>Español</a>
                                      <a href="{{ url('lang/fr') }}" class="item"><i class="flag-icon flag-icon-fr"></i>Français</a>
                                      <a href="{{ url('lang/de') }}" class="item"><i class="flag-icon flag-icon-de"></i>Deutsch</a>
                                      <a href="{{ url('lang/jp') }}" class="item"><i class="flag-icon flag-icon-jp"></i>日本語</a>
                                      <a href="{{ url('lang/in') }}" class="item"><i class="flag-icon flag-icon-in"></i>Hindi</a>
                                      <a href="{{ url('lang/it') }}" class="item"><i class="flag-icon flag-icon-it"></i>Italian</a>
                                      <a href="{{ url('lang/kr') }}" class="item"><i class="flag-icon flag-icon-kr"></i>한국말</a>
                                      <a href="{{ url('lang/my') }}" class="item"><i class="flag-icon flag-icon-my"></i>Malay</a>
                                      <a href="{{ url('lang/nl') }}" class="item"><i class="flag-icon flag-icon-nl"></i>Dutch</a>
                                      <a href="{{ url('lang/ph') }}" class="item"><i class="flag-icon flag-icon-ph"></i>Filipino</a>
                                      <a href="{{ url('lang/pt') }}" class="item"><i class="flag-icon flag-icon-pt"></i>Português</a>
                                    </div>
                              </div>
                            </li>
                            <li class="nav-item">
                                <div class="ui pointing link dropdown item" tabindex="0">
                                    <i class="ui icon linkify"></i> <span class="navmenutext uppercase">{{ __("Quick Access") }}</span>
                                    <i class="dropdown icon"></i>
                                    <div class="menu" tabindex="-1">
                                      <a href="{{ url('clock') }}" target="_blank" class="item"><i class="ui icon clock outline"></i> {{ __("Clock In/Out") }}</a>
                                      <div class="divider"></div>
                                      <a href="{{ url('personal/profile/view') }}"class="item"><i class="ui icon user outline"></i> {{ __("My Profile") }}</a>
                                      <div class="divider"></div>
                                      <a href="{{ url('personal/settings') }}"class="item"><i class="icon file"></i>{{ __("Make a Request") }}</a>
                                    </div>
                              </div>
                            </li>
                            <li class="nav-item">
                               <div class="ui pointing link dropdown item" tabindex="0">
                                   @isset($userAvatar)
                                        <img class="avatar border-white" src="{{ asset('/assets/images/faces/default_user4.jpg') }}" alt="profile photo" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; margin-right: 5px;">
                                    @else
                                        <img class="avatar border-white" src="{{ asset('/assets/images/img/logo4.png') }}" alt="profile photo" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; margin-right: 5px;"/>
                                    @endisset
                                    <div class="menu" tabindex="-1">
                                      <a href="{{ url('personal/update-user') }}" class="item"><i class="ui icon user"></i> {{ __("Update User") }}</a>
                                      <a href="{{ url('personal/update-password') }}" class="item"><i class="ui icon lock"></i> {{ __("Change Password") }}</a>
                                      <hr class="dropdown-divider">
                                        @if(permission::permitted('dashboard') === 'success')
                                         <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ url('dashboard') }}"><i class="lni lni-cog"></i>System Dashboard</a>
                                         @endif
                                         @if(permission::permitted('radio-dashboard') === 'success')
                                         <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ url('/radio/dashboard') }}"><i class="lni lni-rss-feed"></i>Radio Dashboard</a>
                                         @endif
                                      <div class="divider"></div>
                                      <div class="divider"></div>
                                      <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="item" style="border:none; background:none; width:100%; text-align:left; cursor:pointer; padding:0.78571429rem 1.14285714rem;">
                                          <i class="ui icon power"></i> {{ __("Logout") }}
                                        </button>
                                      </form>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item position-relative">
                                <div class="ui pointing link dropdown item" tabindex="0" id="notificationDropdown">
                                    <i class="ui icon bell outline" style="font-size: 1.4rem;"></i>
                                    <span class="badge badge-danger badge-pill"
                                          id="notification-count"
                                          style="position: absolute; top: 2px; right: 2px; font-size: 10px; z-index: 10;">
                                        0
                                    </span>

                                    <div class="menu notifications-dropdown" tabindex="-1" style="min-width: 350px; max-height: 400px; overflow-y: auto;" id="notification-list">
                                        <div class="header d-flex justify-content-between align-items-center">
                                            <span>Notifications</span>
                                            <div>
                                                <button id="markAllAsReadBtn" class="ui icon button mini" title="Mark all as read"><i class="check icon"></i></button>
                                                <button class="ui icon button mini" title="Settings"><i class="cog icon"></i></button>
                                            </div>
                                        </div>

                                        <div class="ui divided relaxed selection list px-2 py-2" id="notification-items">
                                            <div class="item text-center text-muted" id="no-notifications" style="display: none;">
                                                No new notifications.
                                            </div>
                                        </div>

                                        <div class="divider"></div>
                                        <a href="{{ route('personal.notifications') }}" class="item text-center">View All Notifications</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="content">
                @yield('content')
            </div>

            <input type="hidden" id="_url" value="{{ url('/') }}">
        </div>
    </div>


    <script src="{{ asset('/assets/vendor/jquery/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/semantic-ui/semantic.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('/assets/vendor/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('/assets/js/script.js') }}"></script>
    <!--<script src="{{ asset('/assets/js/custom.js') }}"></script>-->
   <div id="personal-config" hidden
         data-fetch="{{ route('notifications.fetch') }}"
         data-mark-all="{{ route('notifications.markAllAsRead') }}"
         data-login="{{ route('login') }}"
         data-session-ping="{{ route('session.ping') }}"
         data-denied="{{ session('denied') ? '1' : '0' }}"
         data-success="{{ session('success') }}"
         data-info="{{ session('info') }}"
         data-error="{{ session('error') }}"
         data-errors='@json($errors->any() ? $errors->all() : [])'
         data-idno="{{ optional(auth()->user())->idno }}"
         data-summary="{{ url('/api/personal/summary') }}"
         data-schedule="{{ url('/api/personal/schedule') }}"
         data-meetings="{{ url('/api/personal/meetings') }}"
         data-leaves="{{ url('/api/personal/leaves') }}"
         data-notifications-api="{{ url('/api/personal/notifications') }}"
         data-profile="{{ url('/api/personal/profile') }}"
         data-team="{{ url('/api/personal/team') }}"
         data-announcements="{{ url('/api/personal/announcements') }}"
         >
    </div>

    @yield('scripts')

<script src="/assets3/personal_layout.js" defer></script>
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
{{-- All inline JS moved to /assets3/personal_layout.js --}}
</body>
</html>
