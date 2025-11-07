<?php

namespace App\Http\Controllers\RadioDashboard\Tech;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TechCheckinsController extends Controller
{
    public function index(Request $request)
    {
        // --------------------------- Filters ---------------------------
        $preset = strtolower((string) $request->input('preset', ''));
        $preset = $preset !== '' ? $preset : null;

        $from = $request->input('from');
        $to   = $request->input('to');

        // Resolve date window
        if ($preset === 'today') {
            $start = Carbon::today();
            $end   = Carbon::today()->endOfDay();
        } elseif ($preset === '7d') {
            $start = Carbon::today()->subDays(6)->startOfDay();
            $end   = Carbon::today()->endOfDay();
        } elseif ($preset === '30d') {
            $start = Carbon::today()->subDays(29)->startOfDay();
            $end   = Carbon::today()->endOfDay();
        } elseif (!empty($from) && !empty($to)) {
            $start = Carbon::parse($from)->startOfDay();
            $end   = Carbon::parse($to)->endOfDay();
        } else {
            // default: last 7 days
            $start = Carbon::today()->subDays(6)->startOfDay();
            $end   = Carbon::today()->endOfDay();
            $preset = '7d';
        }

        $group          = $request->input('group'); // null|shekinah|tekno|other
        $stationIdsIn   = array_values(array_filter((array) $request->input('stations', []), fn($v) => $v !== null && $v !== '' && $v !== false));
        $statusFilter   = array_values(array_filter((array) $request->input('status', []), fn($v) => $v !== null && $v !== '' && $v !== false));
        $responsibility = $request->input('responsibility'); // null|shekinah|landlord
        $techId         = $request->input('tech'); // nullable user id
        $hasNote        = $request->input('has_note'); // null | '1' | '0'

        // Station master list (for filter picklist)
        $stationsAll = DB::table('radio_stations')
            ->orderBy('name')
            ->get(['id','name','on_air']);

        // Effective station set from group + explicit selections
        $stationsQ = DB::table('radio_stations')->select('id','name','on_air');

        if ($group === 'shekinah') {
            $stationsQ->whereRaw("LOWER(name) LIKE 'radio shekinah%'");
        } elseif ($group === 'tekno') {
            $stationsQ->whereRaw("LOWER(name) LIKE 'radio tekno%'");
        } elseif ($group === 'other') {
            $stationsQ->whereRaw("LOWER(name) NOT LIKE 'radio shekinah%' AND LOWER(name) NOT LIKE 'radio tekno%'");
        }

        if (!empty($stationIdsIn)) {
            $stationsQ->whereIn('id', $stationIdsIn);
        }

        $stationsEffective = $stationsQ->orderBy('name')->get();
        $stationIdList = $stationsEffective->pluck('id')->all();

        // If filters produced an empty set, default to all
        if (empty($stationIdList)) {
            $stationIdList = $stationsAll->pluck('id')->all();
            $stationsEffective = $stationsAll;
        }

        // Tech picklist (users seen in checkins)
        $techs = DB::table('users as u')
            ->join('radio_checkins as rc', 'rc.user_id', '=', 'u.id')
            ->select('u.id','u.name')
            ->groupBy('u.id','u.name')
            ->orderBy('u.name')
            ->get();

        // Shared constants
        $incidentStatuses = ['issue','link','power','internet','no_frequency','unspecified','computer_issue','payment_issue'];
        $allKnownStatuses = array_merge(['on_air'], $incidentStatuses, ['offline']);

        // --------------------------- Base queries ---------------------------
        $base = DB::table('radio_checkins as c')
            ->whereBetween('c.created_at', [$start, $end])
            ->whereIn('c.station_id', $stationIdList);

        // Apply optional filters to base
        if (!empty($statusFilter)) {
            $base->whereIn('c.status', $statusFilter);
        }
        if (!empty($responsibility)) {
            $base->where('c.responsibility', $responsibility);
        }
        if (!empty($techId)) {
            $base->where('c.user_id', $techId);
        }
        if ($hasNote === '1') {
            $base->whereNotNull('c.note')->where('c.note','<>','');
        } elseif ($hasNote === '0') {
            $base->where(fn($q) => $q->whereNull('note')->orWhere('note','=',''));
        }

        // --------------------------- KPIs ---------------------------
        $totalStations = count($stationIdList);
        $onAirStations = DB::table('radio_stations')->whereIn('id', $stationIdList)->where('on_air', 1)->count();
        $onAirPct      = $totalStations ? round($onAirStations * 100 / $totalStations) : 0;

        $todayStart = Carbon::today();
        $yStart     = Carbon::yesterday()->startOfDay();
        $yEnd       = Carbon::yesterday()->endOfDay();

        $checkinsToday = DB::table('radio_checkins')
            ->whereBetween('created_at',[$todayStart, $todayStart->copy()->endOfDay()])
            ->whereIn('station_id', $stationIdList)->count();

        $checkinsYesterday = DB::table('radio_checkins')
            ->whereBetween('created_at',[$yStart, $yEnd])
            ->whereIn('station_id', $stationIdList)->count();

        $incidentCount7d = DB::table('radio_checkins')->where('created_at','>=',Carbon::now()->subDays(7))
            ->whereIn('station_id', $stationIdList)
            ->whereIn('status', $incidentStatuses)->count();

        $topIssueRow = DB::table('radio_checkins')->where('created_at','>=',Carbon::now()->subDays(7))
            ->whereIn('station_id', $stationIdList)
            ->whereIn('status', $incidentStatuses)
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->orderByDesc('c')
            ->first();

        $lastCheckRow = DB::table('radio_checkins as rc')
            ->join('radio_stations as rs','rs.id','=','rc.station_id')
            ->leftJoin('users as u','u.id','=','rc.user_id')
            ->whereIn('rc.station_id', $stationIdList)
            ->orderByDesc('rc.created_at')
            ->select('rc.created_at','rs.name as station','u.name as user')
            ->first();

        // Stale count: stations with no check-in in last 24h
        $twentyFourAgo = Carbon::now()->subHours(24);
        $lastCheckPerStation = DB::table('radio_checkins')
            ->whereIn('station_id', $stationIdList)
            ->select('station_id', DB::raw('MAX(created_at) as last_at'))
            ->groupBy('station_id')
            ->pluck('last_at','station_id');

        $staleCount = 0;
        foreach ($stationIdList as $sid) {
            $lastAt = $lastCheckPerStation[$sid] ?? null;
            if (!$lastAt || Carbon::parse($lastAt)->lt($twentyFourAgo)) {
                $staleCount++;
            }
        }

        $kpis = [
            'onAir'             => $onAirStations,
            'total'             => $totalStations,
            'onAirPct'          => $onAirPct,
            'checkinsToday'     => $checkinsToday,
            'checkinsYesterday' => $checkinsYesterday,
            'incidentCount'     => $incidentCount7d,
            'topIssue'          => optional($topIssueRow)->status,
            'lastCheckAt'       => optional($lastCheckRow)->created_at,
            'lastCheckStation'  => optional($lastCheckRow)->station,
            'lastCheckUser'     => optional($lastCheckRow)->user,
            'staleCount'        => $staleCount,
        ];

        // --------------------------- Reports ---------------------------
        // A) Incident Trend (daily counts by status)
        $trendRows = (clone $base)
            ->whereIn('c.status', ['link','power','internet','issue','no_frequency','computer_issue','payment_issue'])
            ->selectRaw('DATE(c.created_at) as d, c.status, COUNT(*) as total')
            ->groupBy('d','c.status')
            ->orderBy('d')
            ->get();

        // Build date index
        $days = [];
        for ($cursor = $start->copy(); $cursor->lte($end); $cursor->addDay()) {
            $days[$cursor->toDateString()] = [
                'date'            => $cursor->toDateString(),
                'link'            => 0,
                'power'           => 0,
                'internet'        => 0,
                'issue'           => 0,
                'no_frequency'    => 0,
                'computer_issue'  => 0,
                'payment_issue'   => 0,
            ];
        }
        foreach ($trendRows as $r) {
            $d = $r->d;
            if (isset($days[$d]) && array_key_exists($r->status, $days[$d])) {
                $days[$d][$r->status] = (int) $r->total;
            }
        }
        $trendDays = array_values($days);

        // B1) Breakdown by status (counts in window)
        $breakdownStatus = array_fill_keys($allKnownStatuses, 0);
        $rawStatus = (clone $base)
            ->select('c.status', DB::raw('COUNT(*) as total'))
            ->groupBy('c.status')
            ->get();
        foreach ($rawStatus as $row) {
            $k = $row->status === 'online' ? 'on_air' : $row->status;
            if (!array_key_exists($k, $breakdownStatus)) {
                $breakdownStatus[$k] = 0;
            }
            $breakdownStatus[$k] += (int) $row->total;
        }

        // B2) Breakdown by responsibility (for incidents only — EXCLUDING on_air)
$breakdownResp = [
    'shekinah' => 0,
    'landlord' => 0,
    'unknown'  => 0,
];

$rawResp = (clone $base)
    // ✅ explicitly exclude on_air from the responsibility math
    ->where('c.status', '<>', 'on_air')
    ->whereNotNull('c.status')
    ->select(
        DB::raw("COALESCE(NULLIF(TRIM(c.responsibility),''),'unknown') as r"),
        DB::raw('COUNT(*) as total')
    )
    ->groupBy('r')
    ->get();

foreach ($rawResp as $row) {
    $key = in_array($row->r, ['shekinah','landlord']) ? $row->r : 'unknown';
    $breakdownResp[$key] += (int) $row->total;
}

        // C) Uptime by Station
        $uptimeRows = (clone $base)
            ->join('radio_stations as rs', 'rs.id', '=', 'c.station_id')
            ->leftJoin('users as u','u.id','=','c.user_id')
            ->select(
                'c.station_id','rs.name',
                DB::raw("SUM(CASE WHEN c.status IN ('on_air','online') THEN 1 ELSE 0 END) as onair"),
                DB::raw("COUNT(*) as ttl"),
                DB::raw("MAX(c.created_at) as last_check")
            )
            ->groupBy('c.station_id','rs.name')
            ->orderBy('rs.name')
            ->get();

        $lastIncidentRows = (clone $base)
            ->whereIn('c.status', $incidentStatuses)
            ->join('radio_stations as rs','rs.id','=','c.station_id')
            ->select('c.station_id','c.status','c.created_at')
            ->orderByDesc('c.created_at')
            ->get()
            ->groupBy('station_id');

        $lastRespRows = (clone $base)
            ->whereIn('c.status', $incidentStatuses)
            ->select('c.station_id','c.responsibility','c.created_at')
            ->orderByDesc('c.created_at')
            ->get()
            ->groupBy('station_id');

        $uptime = [];
        foreach ($uptimeRows as $r) {
            $pct = ($r->ttl > 0) ? round(($r->onair * 100) / $r->ttl) : 0;
            $inc = optional($lastIncidentRows->get($r->station_id)[0] ?? null)->status;
            $resp= optional($lastRespRows->get($r->station_id)[0] ?? null)->responsibility;
            $uptime[] = [
                'station'       => $r->name,
                'pct'           => $pct,
                'last_incident' => $inc ? ucwords(str_replace('_',' ',$inc)) : '—',
                'last_check'    => $r->last_check ? Carbon::parse($r->last_check)->format('Y-m-d H:i') : '—',
                'responsibility'=> $resp ?: '—',
            ];
        }

        // D) Stale / At-Risk list
        $staleStations = [];
        foreach ($stationIdList as $sid) {
            $last = $lastCheckPerStation[$sid] ?? null;
            if (!$last || Carbon::parse($last)->lt($twentyFourAgo)) {
                $name = optional($stationsAll->firstWhere('id',$sid))->name ?: ('#'.$sid);
                $staleStations[] = [
                    'station'    => $name,
                    'hours'      => $last ? Carbon::parse($last)->diffInHours() : '—',
                    'last_check' => $last ? Carbon::parse($last)->format('Y-m-d H:i') : 'Never',
                ];
            }
        }
        usort($staleStations, function($a,$b){
            $ha = is_numeric($a['hours']) ? (int)$a['hours'] : -1;
            $hb = is_numeric($b['hours']) ? (int)$b['hours'] : -1;
            return $hb <=> $ha;
        });

        // E) Latest 20 Incidents
        $latestIncidents = (clone $base)
            ->whereIn('c.status', $incidentStatuses)
            ->join('radio_stations as rs', 'rs.id', '=', 'c.station_id')
            ->leftJoin('users as u','u.id','=','c.user_id')
            ->orderByDesc('c.created_at')
            ->limit(20)
            ->get([
                'c.station_id',
                'rs.name as station',
                'c.status',
                'c.responsibility',
                'c.note',
                DB::raw("COALESCE(u.name,'—') as tech"),
                'c.created_at',
            ])
            ->map(function($x){
                return [
                    'station_id'    => $x->station_id,
                    'station'       => $x->station,
                    'status'        => $x->status,
                    'responsibility'=> $x->responsibility,
                    'note'          => $x->note,
                    'tech'          => $x->tech,
                    'time'          => Carbon::parse($x->created_at)->format('Y-m-d H:i'),
                ];
            })
            ->toArray();

        // --------------------------- Main Log Table ---------------------------
        $checkins = (clone $base)
            ->join('radio_stations as rs', 'rs.id', '=', 'c.station_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->orderByDesc('c.created_at')
            ->get([
                'c.id',
                'c.station_id',
                DB::raw("CASE WHEN c.status='online' THEN 'on_air' ELSE c.status END as status"),
                'c.responsibility',
                'c.note',
                'c.created_at',
                'rs.name as station_name',
                DB::raw("COALESCE(u.name,'—') as tech"),
            ]);

        // Filters payload back to view
        $filters = [
            'from'        => $start->toDateString(),
            'to'          => $end->toDateString(),
            'group'       => $group,
            'stations'    => $stationIdList,
            'stationsAll' => $stationsAll,
            'techs'       => $techs,
        ];

        return view('radio_dashboard.tech.checkins.index', [
            'filters'          => $filters,
            'kpis'             => $kpis,
            'trendDays'        => $trendDays,
            'breakdownStatus'  => $breakdownStatus,
            'breakdownResp'    => $breakdownResp,
            'uptime'           => $uptime,
            'staleStations'    => $staleStations,
            'latestIncidents'  => $latestIncidents,
            'checkins'         => $checkins,
        ]);
    }

    public function store(Request $request)
    {
        // ---- Normalize to snake_case ----
        $rawStatus  = (string) $request->input('status', '');
        $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $rawStatus)));
        $normalized = str_replace(['-', ' '], '_', $normalized);

        // Aliases -> canonical
        $aliasToCanonical = [
            'online'=>'on_air','on'=>'on_air','onair'=>'on_air','on_air'=>'on_air',
            'unspecified_issue'=>'unspecified','no_freq'=>'no_frequency',
            'comp_issue'=>'computer_issue','computer'=>'computer_issue','pc_issue'=>'computer_issue',
            'link_down'=>'link','power_outage'=>'power','internet_down'=>'internet',
            'payment'=>'payment_issue','billing'=>'payment_issue','finance'=>'payment_issue',
        ];
        if (isset($aliasToCanonical[$normalized])) $normalized = $aliasToCanonical[$normalized];
        $request->merge(['status' => $normalized]);

        // Validate
        $allowed = implode(',', [
            'on_air','offline','issue','link','power','internet','no_frequency','unspecified','computer_issue','payment_issue'
        ]);

        try {
            $validated = $request->validate([
                'station_id'     => 'required|integer|exists:radio_stations,id',
                'status'         => "required|in:$allowed",
                'responsibility' => 'nullable|string|max:50',
                'note'           => 'nullable|string|max:1000',
                'reason'         => 'nullable|string|max:255',
            ]);

            // Default note
            if (empty($validated['note'])) {
                switch ($validated['status']) {
                    case 'on_air':         $validated['note'] = 'Station is on air and operating normally.'; break;
                    case 'offline':        $validated['note'] = 'Station is offline. No signal detected.'; break;
                    case 'issue':          $validated['note'] = 'Station reported a technical issue.'; break;
                    case 'link':           $validated['note'] = 'Link is down. Unable to connect to the network.'; break;
                    case 'power':          $validated['note'] = 'Power outage detected at the station.'; break;
                    case 'internet':       $validated['note'] = 'Internet connection is down.'; break;
                    case 'no_frequency':   $validated['note'] = 'No frequency acquired yet for this radio.'; break;
                    case 'unspecified':    $validated['note'] = 'An unspecified issue has been reported.'; break;
                    case 'computer_issue': $validated['note'] = 'A technical issue has been detected with the computer and requires attention.'; break;
                    case 'payment_issue':  $validated['note'] = 'Payment/billing issue affecting service.'; break;
                    default:               $validated['note'] = 'Status updated.'; break;
                }
                if (!empty($validated['responsibility'])) {
                    $validated['note'] .= ucfirst($validated['responsibility']) . ' is responsible for this.';
                }
            }

            $now    = now();
            $userId = Auth::id() ?? 0;

            // ---- 1) Insert check-in (atomic) ----
            DB::beginTransaction();

            $payload = [
                'station_id'     => $validated['station_id'],
                'status'         => $validated['status'],
                'responsibility' => $validated['responsibility'] ?? null,
                'note'           => $validated['note'],
                'user_id'        => $userId,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
            DB::table('radio_checkins')->insert($payload);

            DB::commit();

            // ---- 2) Best-effort side effects (do NOT block success) ----
            try {
                $row = DB::table('radio_stations')
                    ->select('on_air')
                    ->where('id', $validated['station_id'])
                    ->first();

                $nowOnAir = ($validated['status'] === 'on_air') ? 1 : 0;

                $logData = [
                    'station_id' => $validated['station_id'],
                    'was_on_air' => (int)($row->on_air ?? 0),
                    'now_on_air' => $nowOnAir,
                    'changed_by' => $userId,
                    'reason'     => $validated['reason'] ?? 'check-in',
                    'created_at' => $now,
                ];
                if (Schema::hasColumn('radio_station_status_log', 'updated_at')) {
                    $logData['updated_at'] = $now;
                }

                DB::table('radio_station_status_log')->insert($logData);

                DB::table('radio_stations')
                    ->where('id', $validated['station_id'])
                    ->update(['on_air' => $nowOnAir, 'updated_at' => $now]);

            } catch (\Throwable $logEx) {
                Log::warning('Status log write failed (non-fatal)', [
                    'err' => $logEx->getMessage(),
                    'code' => $logEx->getCode(),
                ]);
                if (config('app.debug')) {
                    return back()->with('success', 'Check-in saved (log write skipped).')
                                 ->with('error', 'Note: status log failed — ' . substr($logEx->getMessage(), 0, 200));
                }
            }

            return back()->with('success', 'Check-in saved successfully!');

        } catch (ValidationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->validator)
                ->with('error', 'Please fix the errors highlighted in the form.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('radio_checkins save failed', [
                'err' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Could not save check-in. Please try again.');
        }
    }

    public function toggleStation(Request $request, int $station)
{
    $data = $request->validate([
        'reason'         => ['nullable','string','max:255'],
        'responsibility' => ['nullable','string','max:50'],
    ]);

    $row = DB::table('radio_stations')
        ->where('id', $station)
        ->first(['on_air', 'name']);

    if (! $row) {
        return back()->with('error', 'Station not found.');
    }

    if ((int)($row->on_air ?? 0) === 1) {
        return back()->with('error', "Station '{$row->name}' is already On Air.");
    }

    try {
        DB::transaction(function () use ($station, $row, $data) {
            // Force ON in master table
            DB::table('radio_stations')
                ->where('id', $station)
                ->update([
                    'on_air'     => 1,
                    'updated_at' => now(),
                ]);

            // Status change log
            DB::table('radio_station_status_log')->insert([
                'station_id' => $station,
                'was_on_air' => (int)($row->on_air ?? 0),
                'now_on_air' => 1,
                'changed_by' => Auth::id(),
                'reason'     => $data['reason'] ?? null,
                'created_at' => now(),
            ]);

            // Check-in: when putting ON AIR, responsibility is ALWAYS Shekinah
            DB::table('radio_checkins')->insert([
                'station_id'     => $station,
                'user_id'        => Auth::id(),
                'status'         => 'on_air',
                'responsibility' => 'shekinah', // ← force Shekinah team
                'note'           => 'Back on air (toggle)'.(!empty($data['reason']) ? ' — '.$data['reason'] : ''),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        });

    } catch (\Throwable $e) {
        Log::error('toggle on_air failed', [
            'err'     => $e->getMessage(),
            'station' => $station,
        ]);
        return back()->with('error', 'Could not toggle On-Air. Please try again.');
    }

    return back()->with('success', "Station '{$row->name}' marked On Air (+ check-in + log).");
}

public function quickUpdateStation(Request $request, int $station)
{
    $data = $request->validate([
        'reason'         => ['nullable','string','max:255'],
        'responsibility' => ['nullable','string','max:50'],
    ]);

    $row = DB::table('radio_stations')
        ->where('id', $station)
        ->first(['id','name','on_air']);

    if (!$row) {
        return back()->with('error', 'Station not found.');
    }

    $last = DB::table('radio_checkins')
        ->where('station_id', $station)
        ->orderByDesc('created_at')
        ->first(['status','note']);

    // Normalize legacy values
    $lastStatus = strtolower(str_replace([' ', '-'], '_', (string)($last->status ?? 'offline')));
    if ($lastStatus === 'online') $lastStatus = 'on_air';
    $lastNote   = $last->note ?? null;

    try {
        DB::transaction(function () use ($row, $station, $data, $lastStatus, $lastNote) {
            // If station currently OFF → repeat last known status (does NOT flip anything automatically)
            if ((int)$row->on_air === 0) {
                $writeStatus = $lastStatus ?: 'offline';

                DB::table('radio_checkins')->insert([
                    'station_id'     => $station,
                    'user_id'        => Auth::id(),
                    'status'         => $writeStatus,
                    // If we happen to record ON AIR here, responsibility must be Shekinah; else pass-through.
                    'responsibility' => $writeStatus === 'on_air' ? 'shekinah' : ($data['responsibility'] ?? null),
                    'note'           => $lastNote ?: 'Quick update: repeating last known status.',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                // Keep master in sync with written status
                DB::table('radio_stations')
                    ->where('id', $station)
                    ->update([
                        'on_air'     => ($writeStatus === 'on_air') ? 1 : 0,
                        'updated_at' => now(),
                    ]);

                return;
            }

            // Station currently ON
            if ($lastStatus !== 'on_air') {
                // Bring it back to ON AIR (idempotent)
                DB::table('radio_stations')
                    ->where('id', $station)
                    ->update(['on_air' => 1, 'updated_at' => now()]);

                DB::table('radio_station_status_log')->insert([
                    'station_id' => $station,
                    'was_on_air' => 1,
                    'now_on_air' => 1,
                    'changed_by' => Auth::id(),
                    'reason'     => $data['reason'] ?? null,
                    'created_at' => now(),
                ]);

                DB::table('radio_checkins')->insert([
                    'station_id'     => $station,
                    'user_id'        => Auth::id(),
                    'status'         => 'on_air',
                    'responsibility' => 'shekinah', // ← always Shekinah when ON AIR
                    'note'           => 'Back on air (quick)'.(!empty($data['reason']) ? ' — '.$data['reason'] : ''),
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            } else {
                // Heartbeat while ON AIR
                DB::table('radio_checkins')->insert([
                    'station_id'     => $station,
                    'user_id'        => Auth::id(),
                    'status'         => 'on_air',
                    'responsibility' => 'shekinah', // ← always Shekinah when ON AIR
                    'note'           => 'On air heartbeat (quick)'.(!empty($data['reason']) ? ' — '.$data['reason'] : ''),
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                // Ensure master reflects ON AIR
                DB::table('radio_stations')
                    ->where('id', $station)
                    ->update(['on_air' => 1, 'updated_at' => now()]);
            }
        });
    } catch (\Throwable $e) {
        Log::error('quickUpdateStation failed', ['err' => $e->getMessage(), 'station' => $station]);
        return back()->with('error', 'Could not perform quick update. Please try again.');
    }

    return back()->with('success', "Quick update recorded for '{$row->name}'.");
}
}