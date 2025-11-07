<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Classes\table;
use App\Notifications\SendAlertNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\DailyToken;
use Illuminate\Support\Str;
use App\Http\Controllers\ClockInController;
use App\Providers\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
     protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
{

    // Provide data to the nav only
        View::composer('admin.nav', function ($view) {
            // Cache for 5 minutes to avoid repeated plucks
            $lists = Cache::remember('nav:lists', 300, function () {
                return [
                    // Use the correct sources you actually have
                    'campuses'    => DB::table('tbl_form_campus')->distinct()->pluck('campus')->filter()->values()->toArray(),
                    'ministries'  => DB::table('tbl_form_ministry')->distinct()->pluck('ministry')->filter()->values()->toArray(),
                    'departments' => DB::table('tbl_form_department')->distinct()->pluck('department')->filter()->values()->toArray(),
                ];
            });

            // Recent links for the dropdown
            $meetingsLink = Cache::remember('nav:meetings-links', 120, function () {
                return DB::table('meeting_link')
                    ->orderByDesc('id')
                    ->limit(12)
                    ->get()
                    ->map(function ($m) {
                        $m->url = url('meeting-attendance/'.$m->token);   // token, not meeting_code
                        return $m;
                    });
            });

            $view->with(array_merge($lists, [
                'meetingsLink' => $meetingsLink,
            ]));
        });
    /**
     * 1) Generic avatars (cached)
     */
    $genericAvatars = Cache::rememberForever('generic_avatars_v2', function () {
        $basePath = public_path('assets2/images/avatars');
        $baseRel  = 'assets2/images/avatars'; // relative to /public

        $grab = function (string $sex) use ($basePath, $baseRel) {
            $dir = "$basePath/$sex";
            if (!is_dir($dir)) return [];
            return collect(File::files($dir))
                ->map(fn($f) => '/'.trim("$baseRel/$sex/".$f->getFilename(), '/'))
                ->values()
                ->all();
        };

        return [
            'male'   => $grab('male'),
            'female' => $grab('female'),
        ];
    });

    View::share('genericAvatars', $genericAvatars);

    /**
     * 2) Custom validation rule
     */
    Validator::extend('alpha_dash_space', function ($attribute, $value) {
        return preg_match('/^[\pL\d\s\-\_]+$/u', $value);
    });

    /**
     * 3) Global data (scoped to all views)
     */
    // Limit heavy shared data and avatar resolution to admin views only
    View::composer('admin.*', function ($view) use ($genericAvatars) {
        $view->with('employees', table::people()->get());
        $view->with('campuses', table::campus()->get());

        $user = Auth::user();
        $avatarUrl = null;

        if ($user) {
            // ✅ Match people.id with users.reference
            $person = table::people()
                ->where('id', $user->reference)
                ->select('avatar', 'gender')
                ->first();

            $raw = $person->avatar ?? null;

            if ($raw) {
                if (Str::startsWith($raw, ['http://','https://'])) {
                    $avatarUrl = $raw;
                } elseif (Str::startsWith($raw, ['/'])) {
                    $avatarUrl = $raw;
			} else {
				$candidatePaths = [
					'assets2/faces/'.$raw,
					'assets2/images/faces/'.$raw,
					'assets/faces/'.$raw,
					'assets/images/faces/'.$raw,
				];

				foreach ($candidatePaths as $faceRel) {
					$facePath = public_path($faceRel);
					if (file_exists($facePath)) {
						$avatarUrl = '/'.trim($faceRel, '/');
						break;
					}
				}
			}
            }

            if (!$avatarUrl) {
                // Fallback to generic avatar using gender + stable index
                $pool = strtolower($person->gender ?? 'male');
                $list = $genericAvatars[$pool] ?? [];
                if (!empty($list)) {
                    $index = crc32((string)$user->id) % count($list);
                    $avatarUrl = $list[$index];
                } else {
                    // ultimate fallback
                    $avatarUrl = '/assets2/images/avatar-default.png';
                }
            }
        }

        $view->with('userAvatar', $avatarUrl);
    });

    /**
     * 4) Admin-specific: expose $userGeneric with avatar_url for legacy blades
     */
    View::composer('admin.*', function ($view) {
        $user = Auth::user();
        if ($user) {
            // Prefer already-computed userAvatar from the global composer, if set
            $data = $view->getData();
            $avatarUrl = $data['userAvatar'] ?? null;

            // Add a non-persisted attribute for convenience in blades
            $user->avatar_url = $avatarUrl;
        }
        $view->with('userGeneric', $user);
    });
    

    // 4) Daily QR code generation + sharing
    // Skip heavy DB + file operations when running tests
    if (! app()->environment('testing')) {
        $today    = now()->toDateString();
        $tokenRow = DailyToken::firstOrCreate(
            ['date'  => $today],
            ['token' => Str::random(32)]
        );
        $filename = "{$today}.svg";

        // generate and store SVG if missing
        if (! Storage::disk('public')->exists("qrcodes/{$filename}")) {
            $scanUrl = url('/scan') . '?token=' . $tokenRow->token;
            $svg     = QrCode::size(300)
                             ->format('svg')
                             ->generate($scanUrl);

            Storage::disk('public')->put("qrcodes/{$filename}", $svg);
        }

        // Instead of an absolute URL, just share the relative path
        // that your <img> tag can load directly under public/
        $dailyQrUrl = asset('storage/qrcodes/'.$filename);
        View::share('dailyQrUrl', $dailyQrUrl);
    }

    view()->composer('layouts.new_layout', function ($view) {
        $firstPerson = DB::table('tbl_report_people')
            ->whereNull('status')
            ->orWhere('status', 'active')
            ->orderBy('id', 'asc')
            ->first();

        $view->with('firstPerson', $firstPerson);
    });

    }
}
