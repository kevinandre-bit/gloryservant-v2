<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TimeTrackingDashboardController extends Controller
{
    public function index(Request $request)
    {
        // -------- Filters (with safe defaults) ----------
        $today   = Carbon::now();
        $from    = $request->query('from', $today->copy()->startOfWeek()->toDateString());
        $to      = $request->query('to',   $today->copy()->endOfWeek()->toDateString());
        $groupBy = $request->query('group_by', 'day'); // day|week|month
        if (!in_array($groupBy, ['day','week','month'], true)) $groupBy = 'day';

        $ministryId = $request->query('ministry_id');
        $campusId   = $request->query('campus_id');
        $status     = $request->query('status', 'all'); // all|on_time|late|early|missing_in|missing_out

        $filters = [
            'from'        => $from,
            'to'          => $to,
            'group_by'    => $groupBy,
            'ministry_id' => $ministryId,
            'campus_id'   => $campusId,
            'status'      => $status,
        ];

        // -------- Lookups (IDs are synthetic: MIN(id) per distinct name) ----------
        $ministries = DB::table('tbl_campus_data')
            ->select(DB::raw('MIN(id) as id'), DB::raw('ministry as name'))
            ->whereNotNull('ministry')->where('ministry','<>','')
            ->groupBy('ministry')
            ->orderBy('ministry')
            ->get();

        $campuses = DB::table('tbl_campus_data')
            ->select(DB::raw('MIN(id) as id'), DB::raw('campus as name'))
            ->whereNotNull('campus')->where('campus','<>','')
            ->groupBy('campus')
            ->orderBy('campus')
            ->get();

        // Map selected IDs -> actual string names (lookups above group by the name)
        $selectedMinistryName = null;
        if (!empty($ministryId)) {
            foreach ($ministries as $m) {
                if ((string)$m->id === (string)$ministryId) { $selectedMinistryName = $m->name; break; }
            }
        }
        $selectedCampusName = null;
        if (!empty($campusId)) {
            foreach ($campuses as $c) {
                if ((string)$c->id === (string)$campusId) { $selectedCampusName = $c->name; break; }
            }
        }

        // -------- Base scoped query ----------
        $base = DB::table('tbl_people_attendance as a')
            ->join('tbl_people as p', 'p.id', '=', 'a.reference')
            ->leftJoin('tbl_campus_data as c', 'c.reference', '=', 'p.id')
            ->whereBetween('a.date', [$from, $to]);

        $timeinValueExpr  = "NULLIF(a.timein, '')";
        $timeinTimeExpr   = "TIME(COALESCE({$timeinValueExpr}, CONCAT(a.date, ' 00:00:00')))";
        $timeinTsExpr     = "CASE WHEN {$timeinValueExpr} IS NULL THEN NULL WHEN LENGTH({$timeinValueExpr}) = 8 THEN TIMESTAMP(a.date, STR_TO_DATE({$timeinValueExpr}, '%H:%i:%s')) ELSE CAST({$timeinValueExpr} AS DATETIME) END";
        $timeoutValueExpr = "NULLIF(a.timeout, '')";
        $timeoutTsExpr    = "CASE WHEN {$timeoutValueExpr} IS NULL THEN NULL WHEN LENGTH({$timeoutValueExpr}) = 8 THEN TIMESTAMP(a.date, STR_TO_DATE({$timeoutValueExpr}, '%H:%i:%s')) ELSE CAST({$timeoutValueExpr} AS DATETIME) END";

        $totalHoursRawExpr     = "NULLIF(a.totalhours, '')";
        $totalHoursNumericExpr = "CASE WHEN {$totalHoursRawExpr} IS NULL OR {$totalHoursRawExpr} = '.' THEN NULL ELSE CAST(REPLACE({$totalHoursRawExpr}, ',', '.') AS DECIMAL(10,2)) END";
        $storedMinutesExpr     = "CASE WHEN {$totalHoursNumericExpr} IS NULL THEN NULL ELSE ROUND({$totalHoursNumericExpr} * 60) END";
        $diffMinutesExpr       = "CASE WHEN {$timeinTsExpr} IS NOT NULL AND {$timeoutTsExpr} IS NOT NULL THEN GREATEST(TIMESTAMPDIFF(MINUTE, {$timeinTsExpr}, {$timeoutTsExpr}), 0) ELSE 0 END";
        $workedMinutesExpr     = "COALESCE({$storedMinutesExpr}, {$diffMinutesExpr})";

        if ($selectedMinistryName !== null) $base->where('c.ministry', $selectedMinistryName);
        if ($selectedCampusName   !== null) $base->where('c.campus',   $selectedCampusName);

        // Status filter (optional)
        // On-time <= 08:05:00 ; Late  > 08:05:00 ; Early < 08:00:00
        if ($status === 'on_time') {
            $base->whereRaw("{$timeinValueExpr} IS NOT NULL")->whereRaw("{$timeinTimeExpr} <= '08:05:00'");
        } elseif ($status === 'late') {
            $base->whereRaw("{$timeinValueExpr} IS NOT NULL")->whereRaw("{$timeinTimeExpr}  > '08:05:00'");
        } elseif ($status === 'early') {
            $base->whereRaw("{$timeinValueExpr} IS NOT NULL")->whereRaw("{$timeinTimeExpr}  < '08:00:00'");
        } elseif ($status === 'missing_in') {
            $base->whereRaw("{$timeinValueExpr} IS NULL");
        } elseif ($status === 'missing_out') {
            $base->whereNull('a.timeout');
        }

        // -------- KPIs (current period) ----------
        $kpiRow = (clone $base)
            ->selectRaw('COUNT(DISTINCT a.idno) as unique_people')
            ->selectRaw("SUM(CASE WHEN {$timeinValueExpr} IS NOT NULL AND {$timeinTimeExpr} <= '08:05:00' THEN 1 ELSE 0 END) as on_time_cnt")
            ->selectRaw("SUM(CASE WHEN {$timeinValueExpr} IS NOT NULL AND {$timeinTimeExpr}  > '08:05:00' THEN 1 ELSE 0 END) as late_cnt")
            ->selectRaw("AVG(NULLIF(GREATEST(TIMESTAMPDIFF(MINUTE, TIMESTAMP(a.date, '08:00:00'), {$timeinTsExpr}),0),0)) as avg_minutes_late")
            ->selectRaw('SUM(CASE WHEN a.timeout IS NULL THEN 1 ELSE 0 END) as missed_out')
            ->selectRaw("SUM({$workedMinutesExpr}) as total_minutes")
            ->selectRaw('COUNT(*) as total_rows')
            ->first();

        $totalWithTimeIn = (int) ((clone $base)->whereRaw("{$timeinValueExpr} IS NOT NULL")->count());
        $onTimeRate = $totalWithTimeIn > 0
            ? round(((int)$kpiRow->on_time_cnt) / $totalWithTimeIn * 100, 1)
            : null;

        $totalMinutes    = isset($kpiRow->total_minutes) ? (int) $kpiRow->total_minutes : 0;
        $totalHoursValue = $totalMinutes > 0 ? round($totalMinutes / 60, 1) : 0;

        $kpiCurrent = [
            'unique_people'    => (int) $kpiRow->unique_people,
            'on_time_rate'     => $onTimeRate,
            'late_count'       => (int) $kpiRow->late_cnt,
            'avg_minutes_late' => $kpiRow->avg_minutes_late !== null ? round((float)$kpiRow->avg_minutes_late, 1) : null,
            'missed_out'       => (int) $kpiRow->missed_out,
            'total_hours'      => $totalHoursValue,
        ];

        // -------- KPI deltas (previous period of same length) ----------
        $daysSpan = Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1;
        $prevEnd   = Carbon::parse($from)->subDay()->endOfDay();
        $prevStart = (clone $prevEnd)->subDays($daysSpan-1)->startOfDay();

        $prevBase = DB::table('tbl_people_attendance as a')
            ->join('tbl_people as p', 'p.id', '=', 'a.reference')
            ->leftJoin('tbl_campus_data as c', 'c.reference', '=', 'p.id')
            ->whereBetween('a.date', [$prevStart->toDateString(), $prevEnd->toDateString()]);

        if ($selectedMinistryName !== null) $prevBase->where('c.ministry', $selectedMinistryName);
        if ($selectedCampusName   !== null) $prevBase->where('c.campus',   $selectedCampusName);
        if     ($status === 'on_time')     { $prevBase->whereRaw("{$timeinValueExpr} IS NOT NULL")->whereRaw("{$timeinTimeExpr} <= '08:05:00'"); }
        elseif ($status === 'late')        { $prevBase->whereRaw("{$timeinValueExpr} IS NOT NULL")->whereRaw("{$timeinTimeExpr}  > '08:05:00'"); }
        elseif ($status === 'early')       { $prevBase->whereRaw("{$timeinValueExpr} IS NOT NULL")->whereRaw("{$timeinTimeExpr}  < '08:00:00'"); }
        elseif ($status === 'missing_in')  { $prevBase->whereRaw("{$timeinValueExpr} IS NULL"); }
        elseif ($status === 'missing_out') { $prevBase->whereNull('a.timeout'); }

        $kpiPrevRow = (clone $prevBase)
            ->selectRaw('COUNT(DISTINCT a.idno) as unique_people')
            ->selectRaw("SUM(CASE WHEN {$timeinValueExpr} IS NOT NULL AND {$timeinTimeExpr} <= '08:05:00' THEN 1 ELSE 0 END) as on_time_cnt")
            ->selectRaw("SUM(CASE WHEN {$timeinValueExpr} IS NOT NULL AND {$timeinTimeExpr}  > '08:05:00' THEN 1 ELSE 0 END) as late_cnt")
            ->selectRaw("AVG(NULLIF(GREATEST(TIMESTAMPDIFF(MINUTE, TIMESTAMP(a.date, '08:00:00'), {$timeinTsExpr}),0),0)) as avg_minutes_late")
            ->selectRaw('SUM(CASE WHEN a.timeout IS NULL THEN 1 ELSE 0 END) as missed_out')
            ->selectRaw("SUM({$workedMinutesExpr}) as total_minutes")
            ->first();

        $prevTotalWithTimeIn = (int) ((clone $prevBase)->whereRaw("{$timeinValueExpr} IS NOT NULL")->count());
        $prevOnTimeRate = $prevTotalWithTimeIn > 0
            ? round(((int)$kpiPrevRow->on_time_cnt) / $prevTotalWithTimeIn * 100, 1)
            : null;

        $prevTotalMinutes    = isset($kpiPrevRow->total_minutes) ? (int) $kpiPrevRow->total_minutes : 0;
        $prevTotalHoursValue = $prevTotalMinutes > 0 ? round($prevTotalMinutes / 60, 1) : 0;

        $kpi = [
            'unique_people'    => ['value' => $kpiCurrent['unique_people'],    'delta' => $this->deltaNum($kpiCurrent['unique_people'],    (int)$kpiPrevRow->unique_people)],
            'on_time_rate'     => ['value' => $kpiCurrent['on_time_rate'],     'delta' => $this->deltaNum($kpiCurrent['on_time_rate'],     $prevOnTimeRate)],
            'late_count'       => ['value' => $kpiCurrent['late_count'],       'delta' => $this->deltaNum($kpiCurrent['late_count'],       (int)$kpiPrevRow->late_cnt)],
            'avg_minutes_late' => ['value' => $kpiCurrent['avg_minutes_late'], 'delta' => $this->deltaNum($kpiCurrent['avg_minutes_late'], $kpiPrevRow->avg_minutes_late !== null ? round((float)$kpiPrevRow->avg_minutes_late,1) : null)],
            'missed_out'       => ['value' => $kpiCurrent['missed_out'],       'delta' => $this->deltaNum($kpiCurrent['missed_out'],       (int)$kpiPrevRow->missed_out)],
            'total_hours'      => ['value' => $kpiCurrent['total_hours'],      'delta' => $this->deltaNum($kpiCurrent['total_hours'],      $prevTotalHoursValue)],
        ];

        // -------- Trend (unique people) ----------
        if ($groupBy === 'week') {
            $bucketSelect = "YEARWEEK(a.date, 3) as bucket";
        } elseif ($groupBy === 'month') {
            $bucketSelect = "DATE_FORMAT(a.date, '%Y-%m') as bucket";
        } else {
            $bucketSelect = "DATE_FORMAT(a.date, '%Y-%m-%d') as bucket";
        }

        $trendRows = (clone $base)
            ->selectRaw($bucketSelect)
            ->selectRaw('COUNT(DISTINCT a.idno) as unique_people')
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        $trendLabels = [];
        $trendData   = [];
        foreach ($trendRows as $r) {
            $trendLabels[] = (string) $r->bucket;
            $trendData[]   = (int) $r->unique_people;
        }
        $trend = [
            'labels' => $trendLabels,
            'series' => [
                ['label' => 'Unique People', 'data' => $trendData],
            ],
        ];

        // -------- Stacked punctuality ----------
        $punctRows = (clone $base)
            ->selectRaw($bucketSelect)
            ->selectRaw("SUM(CASE WHEN {$timeinValueExpr} IS NOT NULL AND {$timeinTimeExpr} <= '08:05:00' THEN 1 ELSE 0 END) as on_time")
            ->selectRaw("SUM(CASE WHEN {$timeinValueExpr} IS NOT NULL AND {$timeinTimeExpr}  > '08:05:00' THEN 1 ELSE 0 END) as late")
            ->selectRaw("SUM(CASE WHEN {$timeinValueExpr} IS NOT NULL AND {$timeinTimeExpr}  < '08:00:00' THEN 1 ELSE 0 END) as early")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        $stackLabels = [];
        $onData = []; $lateData = []; $earlyData = [];
        foreach ($punctRows as $r) {
            $stackLabels[] = (string) $r->bucket;
            $onData[]   = (int) $r->on_time;
            $lateData[] = (int) $r->late;
            $earlyData[]= (int) $r->early;
        }
        $stackedPunctuality = [
            'labels' => $stackLabels,
            'series' => [
                ['label' => 'On-time', 'data' => $onData],
                ['label' => 'Late',    'data' => $lateData],
                ['label' => 'Early',   'data' => $earlyData],
            ],
        ];

        // -------- Heatmap (day x hour) ----------
        $heatRaw = (clone $base)
            ->selectRaw('DAYOFWEEK(a.date) as dow')
            ->selectRaw('HOUR(a.timein) as hr')
            ->selectRaw('COUNT(*) as cnt')
            ->whereNotNull('a.timein')
            ->where('a.timein', '<>', '')
            ->groupBy('dow','hr')
            ->get();

        $hours = [];
        for ($h=6; $h<=22; $h++) $hours[] = str_pad($h,2,'0',STR_PAD_LEFT); // 06..22
        $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $grid = [];
        for ($d=0;$d<7;$d++){ $grid[$d] = array_fill(0, count($hours), 0); }

        foreach ($heatRaw as $r) {
            $mysqlDow = (int)$r->dow; // 1=Sun..7=Sat
            $rowIdx = ($mysqlDow === 1) ? 6 : ($mysqlDow - 2); // Sun->6, Mon->0,... Sat->5
            $hr  = (int)$r->hr;
            $colIdx = array_search(str_pad($hr,2,'0',STR_PAD_LEFT), $hours, true);
            if ($colIdx !== false && $rowIdx >= 0 && $rowIdx <= 6) {
                $grid[$rowIdx][$colIdx] = (int)$r->cnt;
            }
        }
        $heatmap = ['hours'=>$hours,'days'=>$days,'values'=>$grid];

        // -------- Ministry breakdown (table) ----------
        $breakRows = (clone $base)
            ->selectRaw('COALESCE(c.ministry,"(Unassigned)") as ministry')
            ->selectRaw('COUNT(DISTINCT a.idno) as unique_people')
            ->selectRaw('COUNT(*) as checkins')
            ->selectRaw("AVG(NULLIF(GREATEST(TIMESTAMPDIFF(MINUTE, TIMESTAMP(a.date, '08:00:00'), {$timeinTsExpr}),0),0)) as avg_minutes_late")
            ->selectRaw("SUM(CASE WHEN {$timeinValueExpr} IS NOT NULL AND {$timeinTimeExpr} <= '08:05:00' THEN 1 ELSE 0 END) as on_time_cnt")
            ->selectRaw("SUM({$workedMinutesExpr}) as total_minutes")
            ->selectRaw("SUM(CASE WHEN a.timeout IS NULL THEN 1 ELSE 0 END) as missed_out")
            ->groupBy('ministry')
            ->orderBy('ministry')
            ->get();

        $teamBreakdown = [];
        foreach ($breakRows as $r) {
            $onRate = null;
            if ((int)$r->checkins > 0) {
                $onRate = round(((int)$r->on_time_cnt) / (int)$r->checkins * 100, 1);
            }
            $teamBreakdown[] = [
                'team'             => (string)$r->ministry,
                'unique_people'    => (int)$r->unique_people,
                'checkins'         => (int)$r->checkins,
                'on_time_rate'     => $onRate,
                'avg_minutes_late' => $r->avg_minutes_late !== null ? round((float)$r->avg_minutes_late,1) : null,
                'total_hours'      => $r->total_minutes !== null ? round(((int)$r->total_minutes)/60,1) : 0,
                'missed_out'       => (int)$r->missed_out,
                'total_minutes'    => (int)$r->total_minutes,
            ];
        }

        // -------- Exceptions (late >10min OR missing in/out), latest first ----------
        $exceptionsRows = (clone $base)
            ->selectRaw("DATE_FORMAT(COALESCE(NULLIF(a.timein,''), CONCAT(a.date, ' 00:00')), '%Y-%m-%d %H:%i') as `when`")
            ->addSelect('a.idno')
            ->selectRaw("TRIM(CONCAT(COALESCE(p.firstname,''),' ',COALESCE(p.lastname,''))) as name")
            ->selectRaw('COALESCE(c.ministry, "(Unassigned)") as team')
            ->selectRaw('COALESCE(c.campus, "(Unknown)") as location')
            ->selectRaw("
                CASE
                  WHEN {$timeinValueExpr} IS NULL THEN 'Missing clock-in'
                  WHEN a.timeout IS NULL THEN 'Missing clock-out'
                  WHEN {$timeinTimeExpr} > '08:15:00' THEN CONCAT('Late ', TIMESTAMPDIFF(MINUTE, TIMESTAMP(a.date, '08:00:00'), {$timeinTsExpr}), ' min')
                  ELSE '—'
                END as issue
            ")
            ->orderBy('a.date', 'desc')
            ->orderBy('a.timein', 'desc')
            ->limit(50)
            ->get();

        $exceptions = [];
        foreach ($exceptionsRows as $row) {
            $exceptions[] = [
                'when'     => $row->when ?: '',
                'idno'     => $row->idno,
                'name'     => $row->name,
                'team'     => $row->team,
                'location' => $row->location,
                'issue'    => $row->issue,
            ];
        }

        // -------- Return view ----------
        return view('admin.time_tracking_dashboard', [
            'filters'            => $filters,
            'ministries'         => $ministries,
            'campuses'           => $campuses,
            'kpi'                => $kpi,
            'trend'              => $trend,
            'stackedPunctuality' => $stackedPunctuality,
            'heatmap'            => $heatmap,
            'teamBreakdown'      => $teamBreakdown,
            'exceptions'         => $exceptions,
        ]);
    }

    public function individual(Request $request)
    {
        $today = Carbon::now();

        $fromParam = $request->query('from', $today->copy()->subDays(6)->toDateString());
        $toParam   = $request->query('to',   $today->toDateString());

        try {
            $fromDate = Carbon::parse($fromParam);
        } catch (\Throwable $e) {
            $fromDate = $today->copy()->subDays(6);
        }

        try {
            $toDate = Carbon::parse($toParam);
        } catch (\Throwable $e) {
            $toDate = $today->copy();
        }

        if ($fromDate->greaterThan($toDate)) {
            [$fromDate, $toDate] = [$toDate->copy(), $fromDate->copy()];
        }

        $from = $fromDate->toDateString();
        $to   = $toDate->toDateString();

        $status = $request->query('status', 'all');
        $allowedStatuses = ['all','on_time','late','early','missing_in','missing_out'];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        $search   = trim((string) $request->query('search', ''));
        $personId = $request->query('person_id');

        $searchForQuery = $search;
        if ($searchForQuery !== '') {
            if (str_contains($searchForQuery, '·')) {
                $searchForQuery = trim(Str::before($searchForQuery, '·'));
            } elseif (str_contains($searchForQuery, '|')) {
                $searchForQuery = trim(Str::before($searchForQuery, '|'));
            } elseif (preg_match('/\(([^)]+)\)$/', $searchForQuery, $matches)) {
                $searchForQuery = trim(Str::beforeLast($searchForQuery, '('));
            }
        }

        $searchQuery = DB::table('tbl_people as p')
            ->leftJoin('tbl_campus_data as c', 'c.reference', '=', 'p.id')
            ->select(
                'p.id',
                DB::raw("COALESCE(NULLIF(c.idno,''), p.id) as idno"),
                DB::raw("TRIM(CONCAT(COALESCE(p.firstname,''),' ',COALESCE(p.lastname,''))) as name"),
                DB::raw('COALESCE(c.ministry, "(Unassigned)") as ministry'),
                DB::raw('COALESCE(c.campus, "(Unknown)") as campus')
            );

        if ($search !== '') {
            $searchQuery->where(function ($q) use ($searchForQuery) {
                $like = '%'.$searchForQuery.'%';
                $q->where('p.firstname', 'like', $like)
                  ->orWhere('p.lastname', 'like', $like)
                  ->orWhere(DB::raw("CONCAT(p.firstname,' ',p.lastname)"), 'like', $like)
                  ->orWhere('c.idno', 'like', $like);
            });
        } else {
            $searchQuery->where('p.employmentstatus', 'Active');
        }

        $searchResults = $searchQuery
            ->orderBy('p.firstname')
            ->orderBy('p.lastname')
            ->when($search !== '', function ($q) {
                $q->limit(2500);
            })
            ->get();

        if (empty($personId) && $searchResults->count() > 0) {
            if ($search !== '' && $searchResults->count() === 1) {
                $personId = $searchResults->first()->id;
            } elseif ($search === '') {
                $personId = $searchResults->first()->id;
            }
        }

        $filters = [
            'from'      => $from,
            'to'        => $to,
            'status'    => $status,
            'search'    => $search,
            'person_id' => $personId,
        ];

        $volunteer = null;
        $volunteerAvatar = null;
        if ($personId) {
            $volunteer = DB::table('tbl_people as p')
                ->leftJoin('tbl_campus_data as c', 'c.reference', '=', 'p.id')
                ->select(
                    'p.id',
                    DB::raw("COALESCE(NULLIF(c.idno,''), p.id) as idno"),
                    DB::raw("TRIM(CONCAT(COALESCE(p.firstname,''),' ',COALESCE(p.lastname,''))) as name"),
                    DB::raw('COALESCE(c.ministry, "(Unassigned)") as ministry'),
                    DB::raw('COALESCE(c.campus, "(Unknown)") as campus'),
                    'p.avatar',
                    'p.gender'
                )
                ->where('p.id', $personId)
                ->first();

            if ($volunteer) {
                $volunteerAvatar = $this->resolveAvatarPath(
                    $volunteer->avatar ?? null,
                    $volunteer->gender ?? null,
                    $volunteer->id ?? null
                );
            }
        }

        $statusOptions = [
            'all'         => 'All records',
            'on_time'     => 'On time (<= 08:05)',
            'late'        => 'Late (> 08:05)',
            'early'       => 'Early (< 08:00)',
            'missing_in'  => 'Missing clock-in',
            'missing_out' => 'Missing clock-out',
        ];

        $cards = [
            'today'    => ['minutes' => 0, 'sessions' => 0],
            'week'     => ['minutes' => 0, 'sessions' => 0],
            'month'    => ['minutes' => 0, 'sessions' => 0, 'overtime_minutes' => 0],
            'overtime' => ['minutes' => 0, 'sessions' => 0],
        ];
        $summary = [
            'worked_minutes'        => 0,
            'overtime_minutes'      => 0,
            'late_minutes'          => 0,
            'late_count'            => 0,
            'sessions'              => 0,
            'avg_clock_in_seconds'  => null,
            'avg_clock_out_seconds' => null,
            'open_sessions'         => 0,
        ];
        $attendanceRecords = [];

        if ($volunteer) {
            $timeinValueExpr  = "NULLIF(a.timein, '')";
            $timeinTimeExpr   = "TIME(COALESCE({$timeinValueExpr}, CONCAT(a.date, ' 00:00:00')))";
            $timeinTsExpr     = "CASE WHEN {$timeinValueExpr} IS NULL THEN NULL WHEN LENGTH({$timeinValueExpr}) = 8 THEN TIMESTAMP(a.date, STR_TO_DATE({$timeinValueExpr}, '%H:%i:%s')) ELSE CAST({$timeinValueExpr} AS DATETIME) END";
            $timeoutValueExpr = "NULLIF(a.timeout, '')";
            $timeoutTsExpr    = "CASE WHEN {$timeoutValueExpr} IS NULL THEN NULL WHEN LENGTH({$timeoutValueExpr}) = 8 THEN TIMESTAMP(a.date, STR_TO_DATE({$timeoutValueExpr}, '%H:%i:%s')) ELSE CAST({$timeoutValueExpr} AS DATETIME) END";

            $totalHoursRawExpr     = "NULLIF(a.totalhours, '')";
            $totalHoursNumericExpr = "CASE WHEN {$totalHoursRawExpr} IS NULL OR {$totalHoursRawExpr} = '.' THEN NULL ELSE CAST(REPLACE({$totalHoursRawExpr}, ',', '.') AS DECIMAL(10,2)) END";
            $storedMinutesExpr     = "CASE WHEN {$totalHoursNumericExpr} IS NULL THEN NULL ELSE ROUND({$totalHoursNumericExpr} * 60) END";
            $diffMinutesExpr       = "CASE WHEN {$timeinTsExpr} IS NOT NULL AND {$timeoutTsExpr} IS NOT NULL THEN GREATEST(TIMESTAMPDIFF(MINUTE, {$timeinTsExpr}, {$timeoutTsExpr}), 0) ELSE 0 END";
            $workedMinutesExpr     = "COALESCE({$storedMinutesExpr}, {$diffMinutesExpr})";
            $lateMinutesExpr     = "CASE WHEN {$timeinValueExpr} IS NOT NULL AND {$timeinTimeExpr}  > '08:05:00' THEN GREATEST(TIMESTAMPDIFF(MINUTE, TIMESTAMP(a.date, '08:00:00'), {$timeinTsExpr}), 0) ELSE 0 END";
            $overtimeMinutesExpr = "GREATEST({$workedMinutesExpr} - 480, 0)";

            $base = DB::table('tbl_people_attendance as a')
                ->where('a.reference', $volunteer->id);

            $applyStatus = function ($query) use ($status, $timeinValueExpr, $timeinTimeExpr, $timeoutValueExpr) {
                if ($status === 'on_time') {
                    $query->whereRaw("{$timeinValueExpr} IS NOT NULL")
                          ->whereRaw("{$timeinTimeExpr} <= '08:05:00'");
                } elseif ($status === 'late') {
                    $query->whereRaw("{$timeinValueExpr} IS NOT NULL")
                          ->whereRaw("{$timeinTimeExpr}  > '08:05:00'");
                } elseif ($status === 'early') {
                    $query->whereRaw("{$timeinValueExpr} IS NOT NULL")
                          ->whereRaw("{$timeinTimeExpr}  < '08:00:00'");
                } elseif ($status === 'missing_in') {
                    $query->whereRaw("{$timeinValueExpr} IS NULL");
                } elseif ($status === 'missing_out') {
                    $query->whereRaw("{$timeoutValueExpr} IS NULL");
                }
            };

            if ($volunteer && !$searchResults->firstWhere('id', $volunteer->id)) {
                $searchResults = $searchResults->prepend((object) [
                    'id'       => $volunteer->id,
                    'idno'     => $volunteer->idno,
                    'name'     => $volunteer->name,
                    'ministry' => $volunteer->ministry,
                    'campus'   => $volunteer->campus,
                ]);
            }

            $rangeBase = (clone $base)->whereBetween('a.date', [$from, $to]);
            $applyStatus($rangeBase);

            $summaryRow = (clone $rangeBase)
                ->selectRaw('COUNT(*) as records_count')
                ->selectRaw("SUM({$workedMinutesExpr}) as worked_minutes")
                ->selectRaw("SUM({$overtimeMinutesExpr}) as overtime_minutes")
                ->selectRaw("SUM({$lateMinutesExpr}) as late_minutes")
                ->selectRaw("SUM(CASE WHEN {$timeinValueExpr} IS NOT NULL AND {$timeinTimeExpr}  > '08:05:00' THEN 1 ELSE 0 END) as late_count")
                ->selectRaw("SUM(CASE WHEN {$timeinValueExpr} IS NOT NULL THEN 1 ELSE 0 END) as sessions_count")
                ->selectRaw("AVG(CASE WHEN {$timeinValueExpr} IS NOT NULL THEN TIME_TO_SEC({$timeinTimeExpr}) END) as avg_clock_in")
                ->selectRaw("AVG(CASE WHEN {$timeoutTsExpr} IS NOT NULL THEN TIME_TO_SEC(TIME({$timeoutTsExpr})) END) as avg_clock_out")
                ->first();

            if ($summaryRow) {
                $summary['worked_minutes']        = (int) ($summaryRow->worked_minutes ?? 0);
                $summary['overtime_minutes']      = (int) ($summaryRow->overtime_minutes ?? 0);
                $summary['late_minutes']          = (int) ($summaryRow->late_minutes ?? 0);
                $summary['late_count']            = (int) ($summaryRow->late_count ?? 0);
                $summary['sessions']              = (int) ($summaryRow->sessions_count ?? 0);
                $summary['avg_clock_in_seconds']  = $summaryRow->avg_clock_in !== null ? (float) $summaryRow->avg_clock_in : null;
                $summary['avg_clock_out_seconds'] = $summaryRow->avg_clock_out !== null ? (float) $summaryRow->avg_clock_out : null;
            }

            $rangeMetrics = function (Carbon $start, Carbon $end) use ($base, $applyStatus, $workedMinutesExpr, $overtimeMinutesExpr, $timeinValueExpr) {
                $query = (clone $base)->whereBetween('a.date', [$start->toDateString(), $end->toDateString()]);
                $applyStatus($query);

                $row = $query
                    ->selectRaw("SUM({$workedMinutesExpr}) as worked_minutes")
                    ->selectRaw("SUM({$overtimeMinutesExpr}) as overtime_minutes")
                    ->selectRaw("SUM(CASE WHEN {$timeinValueExpr} IS NOT NULL THEN 1 ELSE 0 END) as sessions_count")
                    ->first();

                return [
                    'minutes'          => (int) ($row->worked_minutes ?? 0),
                    'overtime_minutes' => (int) ($row->overtime_minutes ?? 0),
                    'sessions'         => (int) ($row->sessions_count ?? 0),
                ];
            };

            $todayStats = $rangeMetrics($today->copy()->startOfDay(), $today->copy()->endOfDay());
            $weekStats  = $rangeMetrics($today->copy()->startOfWeek(), $today->copy()->endOfWeek());
            $monthStats = $rangeMetrics($today->copy()->startOfMonth(), $today->copy()->endOfMonth());

            $cards['today'] = [
                'minutes'  => $todayStats['minutes'],
                'sessions' => $todayStats['sessions'],
            ];
            $cards['week'] = [
                'minutes'  => $weekStats['minutes'],
                'sessions' => $weekStats['sessions'],
            ];
            $cards['month'] = [
                'minutes'          => $monthStats['minutes'],
                'sessions'         => $monthStats['sessions'],
                'overtime_minutes' => $monthStats['overtime_minutes'],
            ];
            $cards['overtime'] = [
                'minutes'  => $monthStats['overtime_minutes'],
                'sessions' => $monthStats['sessions'],
            ];

            $records = (clone $rangeBase)
                ->select(
                    'a.id',
                    'a.date',
                    'a.timein',
                    'a.timeout',
                    'a.status_timein',
                    'a.status_timeout'
                )
                ->selectRaw("{$workedMinutesExpr} as worked_minutes")
                ->selectRaw("{$lateMinutesExpr} as late_minutes")
                ->selectRaw("{$overtimeMinutesExpr} as overtime_minutes")
                ->orderBy('a.date', 'desc')
                ->orderByRaw("COALESCE(NULLIF(a.timein,''), CONCAT(a.date,' 00:00:00')) DESC")
                ->limit(200)
                ->get();

            foreach ($records as $row) {
                try {
                    $dateObj = Carbon::createFromFormat('Y-m-d', $row->date);
                    $dateLabel = $dateObj->format('M d, Y');
                    $weekday   = $dateObj->format('D');
                } catch (\Throwable $e) {
                    $dateLabel = $row->date;
                    $weekday   = '';
                }

                $timeIn  = $this->parseAttendanceTimestamp($row->timein, $row->date);
                $timeOut = $this->parseAttendanceTimestamp($row->timeout, $row->date);

                if ($timeOut === null) {
                    $summary['open_sessions']++;
                }

                $attendanceRecords[] = [
                    'date'             => $row->date,
                    'date_display'     => $dateLabel,
                    'weekday'          => $weekday,
                    'check_in'         => $timeIn ? $timeIn->format('h:i A') : null,
                    'check_out'        => $timeOut ? $timeOut->format('h:i A') : null,
                    'status_in'        => $row->status_timein ?: null,
                    'status_out'       => $row->status_timeout ?: null,
                    'worked_minutes'   => (int) ($row->worked_minutes ?? 0),
                    'late_minutes'     => (int) ($row->late_minutes ?? 0),
                    'overtime_minutes' => (int) ($row->overtime_minutes ?? 0),
                    'open_session'     => $timeOut === null,
                ];
            }
        }

        return view('admin.time_tracking_individual', [
            'filters'       => $filters,
            'volunteer'     => $volunteer,
            'volunteerAvatar' => $volunteerAvatar,
            'searchResults' => $searchResults,
            'statusOptions' => $statusOptions,
            'cards'         => $cards,
            'summary'       => $summary,
            'attendance'    => $attendanceRecords,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        // Simple CSV export of exceptions for the current filter set (extend as needed)
        $request->merge(['status' => 'all']); // keep it broad for export unless you want otherwise
        $response = $this->indexToData($request);
        $rows = $response['exceptions'];

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="time_tracking_export.csv"',
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['When','ID','Name','Ministry','Campus','Issue']);
            foreach ($rows as $r) {
                fputcsv($out, [$r['when'], $r['idno'], $r['name'], $r['team'], $r['location'], $r['issue']]);
            }
            fclose($out);
        }, 200, $headers);
    }

    /**
     * Internal helper to fetch data for export with the same filters.
     */
    private function indexToData(Request $request)
    {
        $today = Carbon::now();
        $from  = $request->query('from', $today->copy()->startOfWeek()->toDateString());
        $to    = $request->query('to',   $today->copy()->endOfWeek()->toDateString());

        $ministryId = $request->query('ministry_id');
        $campusId   = $request->query('campus_id');

        $ministries = DB::table('tbl_campus_data')
            ->select(DB::raw('MIN(id) as id'), DB::raw('ministry as name'))
            ->whereNotNull('ministry')->where('ministry','<>','')
            ->groupBy('ministry')->orderBy('ministry')->get();

        $campuses = DB::table('tbl_campus_data')
            ->select(DB::raw('MIN(id) as id'), DB::raw('campus as name'))
            ->whereNotNull('campus')->where('campus','<>','')
            ->groupBy('campus')->orderBy('campus')->get();

        $selectedMinistryName = null;
        if (!empty($ministryId)) {
            foreach ($ministries as $m) {
                if ((string)$m->id === (string)$ministryId) { $selectedMinistryName = $m->name; break; }
            }
        }
        $selectedCampusName = null;
        if (!empty($campusId)) {
            foreach ($campuses as $c) {
                if ((string)$c->id === (string)$campusId) { $selectedCampusName = $c->name; break; }
            }
        }

        $base = DB::table('tbl_people_attendance as a')
            ->join('tbl_people as p', 'p.id', '=', 'a.reference')
            ->leftJoin('tbl_campus_data as c', 'c.reference', '=', 'p.id')
            ->whereBetween('a.date', [$from, $to]);

        $timeinValueExpr  = "NULLIF(a.timein, '')";
        $timeinTimeExpr   = "TIME(COALESCE({$timeinValueExpr}, CONCAT(a.date, ' 00:00:00')))";
        $timeinTsExpr     = "CASE WHEN {$timeinValueExpr} IS NULL THEN NULL WHEN LENGTH({$timeinValueExpr}) = 8 THEN TIMESTAMP(a.date, STR_TO_DATE({$timeinValueExpr}, '%H:%i:%s')) ELSE CAST({$timeinValueExpr} AS DATETIME) END";
        $timeoutValueExpr = "NULLIF(a.timeout, '')";

        if ($selectedMinistryName !== null) $base->where('c.ministry', $selectedMinistryName);
        if ($selectedCampusName   !== null) $base->where('c.campus',   $selectedCampusName);

        $rows = (clone $base)
            ->selectRaw("DATE_FORMAT(COALESCE(NULLIF(a.timein,''), CONCAT(a.date, ' 00:00')), '%Y-%m-%d %H:%i') as `when`")
            ->addSelect('a.idno')
            ->selectRaw("TRIM(CONCAT(COALESCE(p.firstname,''),' ',COALESCE(p.lastname,''))) as name")
            ->selectRaw('COALESCE(c.ministry, "(Unassigned)") as team')
            ->selectRaw('COALESCE(c.campus, "(Unknown)") as location')
            ->selectRaw("
                CASE
                  WHEN {$timeinValueExpr} IS NULL THEN 'Missing clock-in'
                  WHEN {$timeoutValueExpr} IS NULL THEN 'Missing clock-out'
                  WHEN {$timeinTimeExpr} > '08:15:00' THEN CONCAT('Late ', TIMESTAMPDIFF(MINUTE, TIMESTAMP(a.date, '08:00:00'), {$timeinTsExpr}), ' min')
                  ELSE '—'
                END as issue
            ")
            ->orderBy('a.date', 'desc')
            ->orderBy('a.timein', 'desc')
            ->limit(500)
            ->get();

        $exceptions = [];
        foreach ($rows as $row) {
            $exceptions[] = [
                'when'     => $row->when ?: '',
                'idno'     => $row->idno,
                'name'     => $row->name,
                'team'     => $row->team,
                'location' => $row->location,
                'issue'    => $row->issue,
            ];
        }

        return ['exceptions' => $exceptions];
    }

    private function deltaNum($current, $previous)
    {
        if ($current === null || $previous === null) {
            return null;
        }

        $precision = (is_float($current) || is_float($previous)) ? 1 : 0;
        return round((float)$current - (float)$previous, $precision);
    }

	    private function resolveAvatarPath(?string $rawAvatar, ?string $gender, ?int $seed = null): string
	    {
	        if ($rawAvatar) {
	            if (Str::startsWith($rawAvatar, ['http://', 'https://'])) {
	                return $rawAvatar;
	            }

	            if (Str::startsWith($rawAvatar, ['/'])) {
	                return $rawAvatar;
	            }

	            $candidatePaths = [
	                'assets2/faces/'.$rawAvatar,
	                'assets2/images/faces/'.$rawAvatar,
	                'assets/faces/'.$rawAvatar,
	                'assets/images/faces/'.$rawAvatar,
	            ];

	            foreach ($candidatePaths as $faceRel) {
	                $facePath = public_path($faceRel);
	                if (file_exists($facePath)) {
	                    return '/'.trim($faceRel, '/');
	                }
	            }
	        }

        $genericAvatars = Cache::rememberForever('generic_avatars_v2', function () {
            $basePath = public_path('assets2/images/avatars');
            $baseRel  = 'assets2/images/avatars';

            $grab = function (string $sex) use ($basePath, $baseRel) {
                $dir = "$basePath/$sex";
                if (!is_dir($dir)) {
                    return [];
                }

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

        $pool = strtolower($gender ?? 'male');
        $list = $genericAvatars[$pool] ?? [];
        if (!empty($list)) {
            $index = abs(crc32((string) ($seed ?? random_int(0, PHP_INT_MAX)))) % count($list);
            return $list[$index];
        }

        return '/assets2/images/avatar-default.png';
    }

    public function forceClockOutOpen(Request $request)
    {
        $now = Carbon::now();
        $nowFormatted = $now->format('Y-m-d h:i:s A');

        $openRows = DB::table('tbl_people_attendance')
            ->where(function ($q) {
                $q->whereNull('timeout')
                  ->orWhere('timeout', '')
                  ->orWhere('timeout', '0000-00-00 00:00:00');
            })
            ->where(function ($q) {
                $q->whereNull('totalhours')
                  ->orWhere('totalhours', '')
                  ->orWhere('totalhours', '0');
            })
            ->get(['id', 'idno', 'timein', 'date']);

        $updated = 0;

        foreach ($openRows as $row) {
            $timeIn = $this->parseAttendanceTimestamp($row->timein, $row->date);
            if (!$timeIn) {
                continue;
            }

            $timeOut = $now->greaterThan($timeIn) ? $now->copy() : $timeIn->copy();

            $minutes = max(0, $timeIn->diffInMinutes($timeOut));
            $hoursPart = intdiv($minutes, 60);
            $minutesPart = $minutes % 60;

            $totalFloat = $minutes / 60;
            if ($totalFloat > 15) {
                $totalHours = '6.0';
            } else {
                $totalHours = sprintf('%d.%02d', $hoursPart, $minutesPart);
            }

            DB::table('tbl_people_attendance')
                ->where('id', $row->id)
                ->update([
                    'timeout'        => $nowFormatted,
                    'totalhours'     => $totalHours,
                    'status_timeout' => 'Auto Clock Out',
                    'computer_name'  => 'dashboard-force-clockout',
                ]);

            $updated++;
        }

        if ($updated === 0) {
            return back()->with('clockout_info', 'Everyone is already clocked out or no open sessions found.');
        }

        return back()->with(
            'success',
            'Successfully clocked out '.$updated.' '.Str::plural('person', $updated).' still marked as active.'
        );
    }

    private function parseAttendanceTimestamp(?string $value, ?string $fallbackDate = null): ?Carbon
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);
        $formats = [
            'Y-m-d h:i:s A',
            'Y-m-d h:i A',
            'Y-m-d H:i:s',
            'Y-m-d H:i',
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value);
            } catch (\Exception $e) {
                // try next
            }
        }

        if ($fallbackDate) {
            $timeOnlyFormats = ['h:i:s A', 'h:i A', 'H:i:s', 'H:i'];
            foreach ($timeOnlyFormats as $format) {
                try {
                    $time = Carbon::createFromFormat($format, $value);
                    return Carbon::createFromFormat('Y-m-d H:i:s', $fallbackDate.' '.$time->format('H:i:s'));
                } catch (\Exception $e) {
                    // try next
                }
            }
        }

        return null;
    }
}
