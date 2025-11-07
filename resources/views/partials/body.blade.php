@php
    $segments = request()->segments();
    $lastSegment = last($segments);

    if ($lastSegment === 'admin_v2') {
        $lastSegment = null;
    }

    $page = $lastSegment ? $lastSegment . '.php' : 'index.php';
@endphp

if ($page === 'login.php' || $page === 'login-2.php' || $page === 'register.php' || $page === 'register-2.php' || $page === 'forgot-password.php' || $page === 'forgot-password-2.php' || $page === 'reset-password.php' || $page === 'reset-password-2.php' || $page === 'email-verification.php' || $page === 'email-verification-2.php' || $page === 'two-step-verification.php' || $page === 'two-step-verification-2.php' || $page === 'success.php' || $page === 'success-2.php') {
    echo '<body class="bg-white">';
}
elseif ($page === 'login-3.php' || $page === 'register-3.php' || $page === 'forgot-password-3.php' || $page === 'reset-password-3.php' || $page === 'email-verification-3.php' || $page === 'two-step-verification-3.php' || $page === 'lock-screen.php' || $page === 'error-404.php' || $page === 'error-500.php' || $page === 'under-maintenance.php' || $page === 'under-construction.php') {
    echo '<body class="bg-linear-gradiant">';
}
elseif ($page === 'coming-soon.php') {
    echo '<body class="bg-linear-gradiant d-flex align-items-center justify-content-center">';
}
elseif ($page === 'layout-horizontal.php' || $page === 'layout-horizontal-overlay.php' || $page === 'layout-horizontal-single.php' || $page === 'layout-horizontal-box.php' || $page === 'layout-horizontal-fullwidth.php') {
    echo '<body class="menu-horizontal">';
}
elseif ($page === 'layout-hovered.php') {
    echo '<body class="mini-sidebar expand-menu">';
}
elseif ($page === 'layout-box.php') {
    echo '<body class="mini-sidebar layout-box-mode">';
}
elseif ($page === 'layout-vertical-transparent.php') {
    echo '<body class="data-layout-transparent">';
}
elseif ($page === 'layout-rtl.php') {
    echo '<body class="layout-mode-rtl">';
}
else {
    echo '<body>';
}
?>
