@php
    $segments = request()->segments();
    $lastSegment = last($segments);

    if ($lastSegment === 'admin_v2') {
        $lastSegment = null;
    }

    $page = $lastSegment ? $lastSegment . '.php' : 'index.php';
@endphp

<?php

// Determine HTML tag attributes based on the current page
if ($page == 'layout-horizontal.php') {
    echo '<html lang="en" data-layout="horizontal">';
} elseif ($page == 'layout-detached.php') {
    echo '<html lang="en" data-layout="detached">';
} elseif ($page == 'layout-modern.php') {
    echo '<html lang="en" data-layout="modern">';
} elseif ($page == 'layout-horizontal-overlay.php') {
    echo '<html lang="en" data-layout="horizontal-overlay">';
} elseif ($page == 'layout-two-column.php') {
    echo '<html lang="en" data-layout="twocolumn">';
} elseif ($page == 'layout-hovered.php') {
    echo '<html lang="en" data-layout="layout-hovered">';
} elseif ($page == 'layout-box.php') {
    echo '<html lang="en" data-layout="default" data-width="box">';
} elseif ($page == 'layout-horizontal-single.php') {
    echo '<html lang="en" data-layout="horizontal-single">';
} elseif ($page == 'layout-horizontal-box.php') {
    echo '<html lang="en" data-layout="horizontal-box">';
} elseif ($page == 'layout-horizontal-fullwidth.php') {
    echo '<html lang="en" data-layout="horizontal-fullwidth">';
} elseif ($page == 'layout-horizontal-sidemenu.php') {
    echo '<html lang="en" data-layout="horizontal-sidemenu">';
} elseif ($page == 'layout-stacked.php') {
    echo '<html lang="en"  data-layout="stacked">';
} elseif ($page == 'layout-vertical-transparent.php') {
    echo '<html lang="en" data-layout="transparent">';
} elseif ($page == 'layout-without-header.php') {
    echo '<html lang="en" data-layout="without-header">';
} elseif ($page == 'layout-dark.php') {
    echo '<html lang="en" data-theme="dark">';
} else {
    echo '<html lang="en">';
}
?>
