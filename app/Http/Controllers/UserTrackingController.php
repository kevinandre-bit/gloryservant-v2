<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use GeoIp2\Database\Reader;
use Jenssegers\Agent\Agent;




class UserTrackingController extends Controller
{
 public function trackAction(Request $request)
    {
        // Optional token check: if services.ingest.token is set, require a matching header
        $expected = (string) (config('services.ingest.token') ?? '');
        if ($expected !== '') {
            $provided = $request->header('X-TRACKING-TOKEN');
            if (! $provided) {
                $auth = $request->header('Authorization');
                if ($auth && stripos($auth, 'Bearer ') === 0) {
                    $provided = trim(substr($auth, 7));
                }
            }
            if (! $provided || ! hash_equals($expected, (string)$provided)) {
                return response()->json(['error' => 'unauthorized'], 401, ['Cache-Control' => 'no-store']);
            }
        }
        $sessionId    = $request->input('session_id');
        $page         = $request->input('page');
        $action       = $request->input('action');
        $isInactive   = $request->input('is_inactive', false);
        $userId       = auth()->check() ? auth()->id() : null;
        $timestamp    = now();

        // Basic request info
        $ip           = $request->ip();
        $userAgent    = $request->header('User-Agent');
        $referrer     = $request->header('Referer');
        $utmSource    = $request->input('utm_source');
        $utmCampaign  = $request->input('utm_campaign');
        $routeName    = optional(\Route::current())->getName();
        $path         = $request->path();
        $locale       = $request->header('Accept-Language');
        $isSecure     = $request->isSecure();

        // Device & browser info via Agent
        $agent        = new Agent();
        $agent->setUserAgent($userAgent);
        $deviceType   = $agent->isMobile()
            ? ($agent->isTablet() ? 'tablet' : 'mobile')
            : 'desktop';
        $platform     = $agent->platform();
        $browser      = $agent->browser();
        $browserVersion = $agent->version($browser);

        // Performance & error info (client-side can post these)
        $pageLoadTime = $request->input('performance.load_time');
        $errorData    = $request->input('error_data', []);
        $eventMeta    = $request->input('event_metadata', []);
        $conversion   = $request->input('conversion_flag', false);
        $variant      = $request->input('experiment_variant');

        $dbPath = storage_path('geoip/GeoLite2-City.mmdb');
        try {
            $reader    = new Reader($dbPath);
            $record    = $reader->city($ip);

            $city      = $record->city->name;
            $region    = $record->mostSpecificSubdivision->name;
            $country   = $record->country->name;
            $latitude  = $record->location->latitude;
            $longitude = $record->location->longitude;

            Log::info("GeoIP lookup for {$ip}: {$city}, {$region}, {$country} — ({$latitude},{$longitude})");

        } catch (\Exception $e) {
            Log::warning("GeoIP lookup failed for {$ip}: " . $e->getMessage());

            // Remove external HTTP fallback to avoid sending PII to third parties
            $city = $region = $country = null;
            $latitude = $longitude = null;
        }

        $ipRiskScore = $this->getIpRiskScore($ip);

        // Retrieve any existing session
        $existing = DB::table('user_activity_logs')
            ->where('session_id', $sessionId)
            ->whereNull('session_end')
            ->first();

        // Build session_path log entry
        $pathLog = $existing && $existing->session_path
            ? json_decode($existing->session_path, true)
            : [];
        $pathLog[] = [
            'page'   => $page,
            'action' => $action,
            'at'     => $timestamp->toDateTimeString(),
        ];

        if ($existing) {
            // Update existing session
            $updateData = [
                'action_time'  => $timestamp,
                'session_path' => json_encode($pathLog),
            ];
            if ($action === 'Session Ended') {
                $duration = $timestamp->diffInSeconds(Carbon::parse($existing->session_start));
                $updateData = array_merge($updateData, [
                    'session_end'      => $timestamp,
                    'session_duration' => $duration,
                    'time_spent'       => $duration,
                ]);
                Log::info("Session ended: {$sessionId}");
            }
            DB::table('user_activity_logs')
                ->where('id', $existing->id)
                ->update($updateData);
        } else {
            // Insert new session
            DB::table('user_activity_logs')->insert([
                'user_id'           => $userId,
                'session_id'        => $sessionId,
                'ip_address'        => $ip,
                'user_agent'        => $userAgent,
                'referrer'          => $referrer,
                'utm_source'        => $utmSource,
                'utm_campaign'      => $utmCampaign,
                'route_name'        => $routeName,
                'path'              => $path,
                'session_path'      => json_encode($pathLog),
                'city'              => $city,
                'region'            => $region,
                'country'           => $country,
                'latitude'          => $latitude,
                'longitude'         => $longitude,
                'locale'            => $locale,
                'is_https'          => $isSecure,
                'page_load_time'    => $pageLoadTime,
                'error_data'        => json_encode($errorData),
                'event_metadata'    => json_encode($eventMeta),
                'conversion_flag'   => $conversion,
                'experiment_variant'=> $variant,
                'ip_risk_score'     => $ipRiskScore,
                'device_type'       => $deviceType,
                'platform'          => $platform,
                'browser'           => $browser,
                'browser_version'   => $browserVersion,
                'page'              => $page,
                'action'            => $action,
                'session_start'     => $timestamp,
                'action_time'       => $timestamp,
            ]);
            Log::info("New session started: {$sessionId}");
        }

        return response()->json(['message' => 'Action logged successfully']);
    }



    /**
     * Stub for IP risk scoring service
     */
    protected function getIpRiskScore(string $ip): float
    {
        // Hook into your risk-scoring API here
        return 0.0;
    }

    // removed external HTTP IP lookup fallback for privacy
    public function showReport()
{
    
    $now = Carbon::now();
$thisWeekStart = $now->copy()->startOfWeek();
$lastWeekStart = $now->copy()->subWeek()->startOfWeek();
$lastWeekEnd = $thisWeekStart->copy()->subSecond(); // 1 second before this week starts

$logsThisWeek = DB::table('user_activity_logs')
    ->whereBetween('created_at', [$thisWeekStart, $now])
    ->count();

$logsLastWeek = DB::table('user_activity_logs')
    ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
    ->count();

$weeklyGrowth = 0;

if ($logsLastWeek > 0) {
    $weeklyGrowth = (($logsThisWeek - $logsLastWeek) / $logsLastWeek) * 100;
} elseif ($logsThisWeek > 0) {
    $weeklyGrowth = 100;
}

    $logs = DB::table('user_activity_logs as logs')
                ->leftJoin('users', 'logs.user_id', '=', 'users.id')
                ->leftJoin('tbl_campus_data as cd', 'users.id', '=', 'cd.reference')
                ->select(
                    'logs.id',
                    'logs.session_id',
                    'logs.ip_address',
                    'logs.city',
                    'logs.country',
                    'logs.page',
                    'logs.action',
                    'logs.session_start',
                    'logs.session_end',
                    'logs.session_duration',
                    'users.name as username',
                    'cd.campus',
                    'cd.ministry'
                )
                ->orderBy('logs.action_time', 'desc')
                ->paginate(20);

    return view('admin.reports.user-activity', compact('logs', 'weeklyGrowth'));
}


}
