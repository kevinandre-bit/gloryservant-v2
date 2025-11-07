<?php

namespace App\Http\Controllers\RadioDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
   public function index(Request $request)
    {
        // ---------- KPIs (cached) ----------
        $kpis = Cache::remember('radio_snapshot_kpis', 300, function () {
            $now          = now();
            $startToday   = $now->copy()->startOfDay();
            $startYday    = $now->copy()->subDay()->startOfDay();
            $endYday      = $startToday;
            $sevenDaysAgo = $now->copy()->subDays(7);
            $incidentStatuses = ['issue', 'link', 'power'];

            // Availability (current)
            $totalStations = DB::table('radio_stations')->count();
            $onAirStations = DB::table('radio_stations')->where('on_air', 1)->count();
            $onAirPct      = $totalStations ? round($onAirStations * 100 / $totalStations) : 0;

            // Activity (today/yesterday)
            $checkinsToday = DB::table('radio_checkins')
                ->where('created_at', '>=', $startToday)
                ->count();

            $checkinsYesterday = DB::table('radio_checkins')
                ->where('created_at', '>=', $startYday)
                ->where('created_at', '<',  $endYday)
                ->count();

            // Incidents (7d) & top issue
            $incidentCount = DB::table('radio_checkins')
                ->where('created_at', '>=', $sevenDaysAgo)
                ->whereIn('status', $incidentStatuses)
                ->count();

            $topIssueRow = DB::table('radio_checkins')
                ->select('status', DB::raw('COUNT(*) as c'))
                ->where('created_at', '>=', $sevenDaysAgo)
                ->whereIn('status', $incidentStatuses)
                ->groupBy('status')
                ->orderByDesc('c')
                ->first();

            $topIssue = $topIssueRow->status ?? null;

            // Last check-in (latest record + station/user)
            $lastCheckRow = DB::table('radio_checkins as rc')
                ->join('radio_stations as rs', 'rs.id', '=', 'rc.station_id')
                ->leftJoin('users as u', 'u.id', '=', 'rc.user_id')
                ->orderByDesc('rc.created_at')
                ->select('rc.created_at', 'rs.name as station_name', 'u.name as user_name')
                ->first();

            // Group split (Shekinah vs Tekno) – optional
            $groupRows = DB::table('radio_stations')
                ->selectRaw("
                    CASE
                      WHEN LOWER(name) LIKE 'radio shekinah%' THEN 'shekinah'
                      WHEN LOWER(name) LIKE 'radio tekno%'    THEN 'tekno'
                      ELSE 'other'
                    END as g
                ")
                ->selectRaw("SUM(CASE WHEN on_air = 1 THEN 1 ELSE 0 END) as on_air")
                ->selectRaw("COUNT(*) as total")
                ->groupBy('g')
                ->get()
                ->keyBy('g');

            $shekOnAir  = (int) ($groupRows['shekinah']->on_air ?? 0);
            $shekTotal  = (int) ($groupRows['shekinah']->total ?? 0);
            $teknoOnAir = (int) ($groupRows['tekno']->on_air ?? 0);
            $teknoTotal = (int) ($groupRows['tekno']->total ?? 0);

            return [
                // Tile 1: Stations On Air
                'onAir'        => $onAirStations,
                'total'        => $totalStations,
                'onAirPct'     => $onAirPct,

                // Tile 2: Check-ins Today
                'checkinsToday'     => $checkinsToday,
                'checkinsYesterday' => $checkinsYesterday,

                // Tile 3: Incidents (7d)
                'incidentCount' => $incidentCount,
                'topIssue'      => $topIssue, // may be null

                // Tile 4: Last Check-in
                'lastCheckAt'       => optional($lastCheckRow)->created_at,
                'lastCheckStation'  => $lastCheckRow->station_name ?? null,
                'lastCheckUser'     => $lastCheckRow->user_name ?? null,

                // Optional group split
                'shekOnAir'  => $shekOnAir,
                'shekTotal'  => $shekTotal,
                'teknoOnAir' => $teknoOnAir,
                'teknoTotal' => $teknoTotal,

                // Misc
                'lastUpdate' => $now,
            ];
        });

        // ---------- Sites (snapshot w/ derived ui_status) ----------
        $now = Carbon::now();
        $recentWindowStart = $now->copy()->subHours(24);

        $latestPerStation = DB::table('radio_checkins as rc2')
            ->select('rc2.station_id', DB::raw('MAX(rc2.created_at) as max_created_at'))
            ->groupBy('rc2.station_id');

        $sites = DB::table('radio_stations as rs')
            ->leftJoinSub($latestPerStation, 'l', function ($join) {
                $join->on('l.station_id', '=', 'rs.id');
            })
            ->leftJoin('radio_checkins as rc', function ($join) {
                $join->on('rc.station_id', '=', 'rs.id')
                     ->on('rc.created_at', '=', 'l.max_created_at');
            })
            ->select([
                'rs.id',
                'rs.name',
                'rs.on_air',
                'rs.frequency',
                'rs.frequency_status',
                'rc.status as last_status',
                'rc.note as last_note',
                'rc.created_at as last_check',
            ])
            ->orderBy('rs.name')
            ->get()
            ->map(function ($s) use ($recentWindowStart) {
            $recent = $s->last_check ? Carbon::parse($s->last_check) : null;

            // Normalize last_status to our canonical set
            $last = strtolower(trim((string)$s->last_status));
            $last = str_replace([' ', '-'], '_', $last);

            // map legacy values
            if ($last === 'online') $last = 'on_air';

            // Decide ui_status:
            if ((int)$s->on_air === 1) {
                $s->ui_status = 'on_air';
            } elseif ($recent && $recent->gte($recentWindowStart) && in_array($last, [
                'on_air','issue','power','link','internet','no_frequency','unspecified','offline'
            ], true)) {
                $s->ui_status = $last;
            } else {
                $s->ui_status = 'offline';
            }

            return $s;
        });

        // ---------- Activity feed ----------
        $activity = DB::table('radio_checkins as rc')
            ->join('radio_stations as rs', 'rs.id', '=', 'rc.station_id')
            ->orderByDesc('rc.created_at')
            ->limit(20)
            ->get([
                'rc.id',
                'rs.name as station_name',
                'rc.status',
                'rc.note',
                'rc.created_at',
            ])
            ->map(function ($a) {
                $a->tag = $a->status;
                $map = [
                    'Online'      => 'emerald',
                    'offline' => 'rose',
                    'issue'   => 'amber',
                    'power'   => 'slate',
                    'link'    => 'sky',
                ];
                $lower = $a->tag ? strtolower($a->tag) : null;
                $a->tag_color = $map[$lower] ?? 'slate';
                $a->created_at = Carbon::parse($a->created_at);
                $a->text = "{$a->station_name}: {$a->status}" . ($a->note ? " — {$a->note}" : '');
                return $a;
            });

        // ---------- Assignments ----------
        $assignments = DB::table('radio_assignments')
            ->whereNotIn('status', ['Done','Closed'])
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // ---------- Maintenance snapshot ----------
        $today = Carbon::today();

        $overdueCount = DB::table('radio_maintenance')
            ->where('status', '<>', 'Closed')
            ->where('due', '<', $today)
            ->count();

        $topFaults = DB::table('radio_maintenance')
            ->where('status', '<>', 'Closed')
            ->select('title', DB::raw('COUNT(*) as total'))
            ->groupBy('title')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ---------- Misc KPIs ----------
        $since = $now->copy()->subHours(24);

        $kpiAlerts = DB::table('radio_checkins')
            ->where('created_at', '>=', $since)
            ->whereIn('status', ['issue','power','link'])
            ->count();

        $lastUpdate = DB::table('radio_checkins')->max('created_at');

        // ---------- Stations for modal ----------
        $stations = DB::table('radio_stations')
            ->orderBy('name')
            ->get(['id','name']);

        // Merge KPIs into top-level view data so Blade can use $onAir, $total, etc.
        return view('radio_dashboard.dashboard', array_merge([
            'sites'        => $sites,
            'activity'     => $activity,
            'assignments'  => $assignments,
            'overdueCount' => $overdueCount,
            'topFaults'    => $topFaults,
            'kpiAlerts'    => $kpiAlerts,
            'lastUpdate'   => $lastUpdate,
            'stations'     => $stations,
        ], $kpis));
    }

    // === Optional POST handlers for your modals ===

    public function storeOutage(Request $request)
{
    $provided = (string) ($request->header('X-Webhook-Token') ?? '');
    $expected = (string) (config('services.checkins.webhook_token') ?? '');

    if ($expected === '') {
        abort(500, 'Webhook secret not configured');
    }
    if (! hash_equals($expected, $provided)) {
        abort(403, 'Invalid webhook token');
    }

    $data = $request->validate([
        'station_id'     => ['required', 'integer', 'exists:radio_stations,id'],
        'status'         => ['required','in:online,offline,issue,link,power,internet,no_frequency,unspecified'],
        'responsibility' => ['nullable', 'string', 'max:50'],
        'note'           => ['nullable', 'string', 'max:1000'],
    ]);

    // Normalize to canonical 'on_air' or incident buckets
    $status = strtolower($data['status']);
    if ($status === 'online') {
        $status = 'on_air';
    }

    // Default notes
    if (empty($data['note'])) {
        switch ($status) {
            case 'on_air':
                $data['note'] = 'Station is online and operating normally.';
                break;
            case 'offline':
                $data['note'] = 'Station is offline. No signal detected.';
                break;
            case 'issue':
                $data['note'] = 'Station reported a technical issue.';
                break;
            case 'link':
                $data['note'] = 'Link is down. Unable to connect to the network.';
                break;
            case 'power':
                $data['note'] = 'Power outage detected at the station.';
                break;
            case 'internet':
                $data['note'] = 'Internet connection is down.';
                break;
            case 'no_frequency':
                $data['note'] = 'No frequency acquired yet for this radio.';
                break;
            case 'unspecified':
                $data['note'] = 'An unspecified issue has been reported.';
                break;
            default:
                $data['note'] = 'Status updated automatically.';
        }
    }

    try {
        DB::transaction(function () use ($data, $status) {
            // 1) Insert check-in
            DB::table('radio_checkins')->insert([
                'station_id'     => $data['station_id'],
                'user_id'        => null,
                'status'         => $status,
                'responsibility' => $data['responsibility'] ?? null,
                'note'           => $data['note'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // 2) Update master on_air based on the status
            DB::table('radio_stations')
                ->where('id', $data['station_id'])
                ->update([
                    'on_air'     => ($status === 'on_air') ? 1 : 0,
                    'updated_at' => now(),
                ]);
        });

    } catch (\Throwable $e) {
        Log::error('storeOutage failed', ['err'=>$e->getMessage(), 'payload'=>$data]);
        return response()->json(['success' => false, 'error' => 'Could not record outage.'], 500);
    }

    return response()->json(['success' => true]);
}

    public function storeAssignment(Request $request)
    {
        $data = $request->validate([
            'title'    => ['required','string','max:255'],
            'tech'     => ['required','string','max:100'],
            'site'     => ['required','string','max:255'], // stored as name per your schema
            'window'   => ['required','string','max:100'],
            'priority' => ['required','in:Low,Normal,High'],
            'status'   => ['required','string','max:50'],
            'desc'     => ['nullable','string'],
            'dept'     => ['nullable','string','max:100'],
        ]);

        DB::table('radio_assignments')->insert(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return back()->with('success', 'Assignment created.');
    }

    public function storeMaintenance(Request $request)
    {
        $data = $request->validate([
            'title'    => ['required','string','max:255'],
            'site'     => ['required','string','max:255'],
            'type'     => ['required','in:Preventive,Corrective'],
            'priority' => ['required','in:Low,Medium,High'],
            'due'      => ['required','date'],
            'assignee' => ['nullable','string','max:100'],
            'notes'    => ['nullable','string'],
            'status'   => ['sometimes','in:Open,In Progress,Closed'],
        ]);

        DB::table('radio_maintenance')->insert(array_merge($data, [
            'status'     => $data['status'] ?? 'Open',
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return back()->with('success', 'Maintenance ticket created.');
    }

    public function storeDailyReport(Request $request)
{
    $data = $request->validate([
        'report_date' => ['required','date'],
        'role'        => ['required','in:technician,operator,admin'],
        'notes'       => ['nullable','string'],
    ]);

    // TODO: persist to your real reports table when ready.
    // For now, just log or stash it in a placeholder table if you have one.
    // DB::table('daily_reports')->insert([...]);

    return back()->with('success', 'Daily report request received.');
}

public function announceSchedule(Request $request)
{
    $data = $request->validate([
        'effective_date' => ['required','date'],
        'scope'          => ['required','string','max:255'], // 'all' or a site name
        'message'        => ['required','string','max:2000'],
    ]);

    // TODO: replace with your real announcement/broadcast logic.
    // Example stub: store in a table or dispatch a notification/event.
    // DB::table('schedule_announcements')->insert([...]);

    return back()->with('success', 'Schedule change announcement queued.');
}

public function triggerFlow(Request $request)
{
    // The Zoho Flow webhook URL you pasted:
    $url = config('services.zoho.flow_url');

    // Example payload — you decide what fields you want Flow to receive
    $payload = [
        'station_id' => $request->query('station_id', 0),
        'status'     => $request->query('status', 'online'),
        'note'       => $request->query('note', 'triggered via link'),
    ];

    // POST to Zoho Flow
    $resp = Http::asForm()->post($url, $payload);

    if (! $resp->successful()) {
        return back()->with('error', 'Zoho Flow call failed: '.$resp->body());
    }

    return back()->with('success', 'Zoho Flow trigger fired.');
}

}