@php
    $route = request()->route();
    $page = '';

    if ($route) {
        if (method_exists($route, 'getName')) {
            $page = (string) $route->getName();
        }

        if ($page === '' && method_exists($route, 'uri')) {
            $page = (string) $route->uri();
        }
    }

    if ($page === '') {
        $page = request()->path();
    }

    $page = trim($page, '/');

    if ($page === '') {
        $segments = array_filter(explode('/', request()->path()));
        $page = end($segments) ?: '';
    }

    $excluded = [
        'under-maintenance',
        'under-maintenance.php',
        'under-construction',
        'under-construction.php',
        'coming-soon',
        'coming-soon.php',
        'error-404',
        'error-404.php',
        'error-500',
        'error-500.php',
        'two-step-verification-3',
        'two-step-verification-3.php',
        'two-step-verification-2',
        'two-step-verification-2.php',
        'two-step-verification',
        'two-step-verification.php',
        'email-verification-3',
        'email-verification-3.php',
        'email-verification-2',
        'email-verification-2.php',
        'email-verification',
        'email-verification.php',
        'reset-password-3',
        'reset-password-3.php',
        'reset-password-2',
        'reset-password-2.php',
        'reset-password',
        'reset-password.php',
        'forgot-password-3',
        'forgot-password-3.php',
        'forgot-password-2',
        'forgot-password-2.php',
        'forgot-password',
        'forgot-password.php',
        'register-3',
        'register-3.php',
        'register-2',
        'register-2.php',
        'register',
        'register.php',
        'login-3',
        'login-3.php',
        'login-2',
        'login-2.php',
        'login',
        'login.php',
        'success',
        'success.php',
        'success-2',
        'success-2.php',
        'success-3',
        'success-3.php',
        'lock-screen',
        'lock-screen.php',
        'job-grid-2',
        'job-grid-2.php',
        'job-list-2',
        'job-list-2.php',
        'job-details',
        'job-details.php',
    ];

    $jobPages = [
        'job-grid-2',
        'job-grid-2.php',
        'job-list-2',
        'job-list-2.php',
        'job-details',
        'job-details.php',
    ];
@endphp

@unless(in_array($page, $excluded, true))
    @include('partials.topbar')
    @include('partials.sidebar')
    @include('partials.horizontal-sidebar')
    @include('partials.twocolumn-sidebar')
    @include('partials.stacked-sidebar')
@endunless

@if(in_array($page, $jobPages, true))
    @include('partials.job-header')
@endif
