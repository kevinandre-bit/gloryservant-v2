<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Index Only)
|--------------------------------------------------------------------------
| ⚠️ Do not define routes directly in this file.
| This file only organizes and loads grouped partials.
|
| Groups:
| 1) Public      2) Personal      3) MR
| 4) Admin (non-Radio)             5) Radio
| 6) Special (fallbacks/webhooks/maintenance/etc.)
|
| Run `php artisan route:list` before & after to verify no changes.
*/

// 1) Public
require base_path('routes/web.public.php');

// 2) Personal
require base_path('routes/web.personal.php');

// 3) MR
require base_path('routes/web.mr.php');

// 4) Admin (non-Radio)
require base_path('routes/web.admin.php');

// 5) Radio
require base_path('routes/web.radio.php');

// 6) Special
require base_path('routes/web.special.php');