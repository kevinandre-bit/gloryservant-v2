@php
    use Illuminate\Support\Str;

    // Get current route name (e.g. src.employee-list)
    $route = Route::currentRouteName() ?? 'src.index';
    $page = Str::after($route, 'src.'); // remove 'src.' prefix

    $acronyms = ['ui', 'ai', 'js', 'api', 'css', 'html', 'php', 'seo', 'ip', 'faq', 'pos', 'rtl'];

    if ($page === 'index') {
        $title = 'Admin Dashboard';
    } else {
        // Clean and format title
        $parts = explode('-', str_replace('ui-', '', strtolower($page)));

        $hasIcon = false;
        $hasChart = false;
        $cleanedParts = [];

        foreach ($parts as $part) {
            if (is_numeric($part)) continue;
            if ($part === 'icon') { $hasIcon = true; continue; }
            if ($part === 'chart') { $hasChart = true; continue; }
            if ($part === 'all') continue;
            $cleanedParts[] = $part;
        }

        $formattedParts = array_map(function ($word) use ($acronyms) {
            return in_array($word, $acronyms) ? strtoupper($word) : ucfirst($word);
        }, $cleanedParts);

        if ($hasIcon) $formattedParts[] = 'Icons';
        if ($hasChart) $formattedParts[] = 'Charts';

        $title = implode(' ', $formattedParts);
    }
@endphp

<!-- Meta Tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title }} | Glory Servant – A powerful church management platform that helps ministries handle member records, attendance, payroll, scheduling, and team coordination in one place.</title>
<meta name="description" content="Glory Servan">
<meta name="keywords" content="Glory Servant">
<meta name="author" content="Tabernacle of Glory">
<meta name="robots" content="index, follow">