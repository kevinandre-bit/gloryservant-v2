<?php

namespace App\Http\Controllers\Admin;
use DB;
use App\Classes\table;
use App\Classes\permission;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;       

class DashboardController extends Controller
{

    public function index(Request $request) 
    {
        // Permission check
        if (permission::permitted('dashboard') == 'fail') {
            return redirect()->route('denied');
        }

        // ── Existing “online/offline” logic ──────────────────────────────────────
        $datenow       = date('Y-m-d'); 
        $is_online     = table::attendance()->where('date', $datenow)->pluck('reference');
        $is_online_arr = $is_online->toArray();
        $is_online_now = count($is_online_arr);

        $emp_ids       = table::campusdata()->pluck('reference');
        $emp_ids_arr   = $emp_ids->toArray(); 
        $is_offline_now = count(array_diff($emp_ids_arr, $is_online_arr));

        $tf = table::settings()->value('time_format');

        $emp_all_type      = table::people()
            ->join('tbl_campus_data', 'tbl_people.id', '=', 'tbl_campus_data.reference')
            ->where('tbl_people.employmentstatus', 'Active')
            ->orderBy('tbl_campus_data.startdate', 'desc')
            ->take(8)
            ->get();

        $emp_typeR         = table::people()
            ->where('employmenttype', 'Regular')
            ->where('employmentstatus', 'Active')
            ->count();

        $emp_typeT         = table::people()
            ->where('employmenttype', 'Trainee')
            ->where('employmentstatus', 'Active')
            ->count();

        $emp_allActive     = table::people()
            ->where('employmentstatus', 'Active')
            ->count();

        $recent_attendance = table::attendance()
            ->latest('date')
            ->take(8)
            ->get();

        $emp_approved_leave = table::leaves()
            ->where('status', 'Approved')
            ->orderBy('leavefrom', 'desc')
            ->take(8)
            ->get();

        $emp_leaves_approve = table::leaves()
            ->where('status', 'Approved')
            ->count();

        $completed_tasks   = DB::table('volunteer_tasks')
            ->where('completion_status', 'completed')
            ->sum('task_count');

        $pending_tasks     = DB::table('volunteer_tasks')
            ->where('completion_status', 'pending')
            ->count();

        $emp_leaves_pending = table::leaves()
            ->where('status', 'Pending')
            ->count();

        $emp_leaves_all     = table::leaves()
            ->whereIn('status', ['Approved', 'Pending'])
            ->count();

        $birthdaysToday = DB::table('tbl_people')
            ->join('tbl_campus_data', 'tbl_people.id', '=', 'tbl_campus_data.reference')
            ->select('tbl_people.firstname', 'tbl_people.lastname', 'tbl_campus_data.ministry', 'tbl_campus_data.campus')
            ->whereRaw('DATE_FORMAT(birthday, "%m-%d") = DATE_FORMAT(CURDATE(), "%m-%d")')
            ->get();

        $users       = table::people()->get();
        $campuses   = table::campus()->get();
        $ministries = table::ministry()->get();
        // ────────────────────────────────────────────────────────────────────────────

        // ── WEEK-OVER-WEEK ATTENDANCE COMPARISON ─────────────────────────────────────

$today         = Carbon::today()->toDateString();
$lastWeekDay   = Carbon::today()->subWeek()->toDateString();

// count records
$todayCount    = table::attendance()
                     ->whereDate('date', $today)
                     ->count();

$lastWeekCount = table::attendance()
                     ->whereDate('date', $lastWeekDay)
                     ->count() ?: 1;  // avoid division by zero

// calculate week-over-week growth rate
$growthVsLastWeek = round((($todayCount - $lastWeekCount) / $lastWeekCount) * 100, 1);

// (optional) other metric if you still want total employees
$totalEmployees   = table::people()
                     ->where('employmentstatus', 'Active')
                     ->count() ?: 1;

$attendancePercent = round(($todayCount / $totalEmployees) * 100);
        // ────────────────────────────────────────────────────────────────────────────

// ── SCHEDULE-BASED WEEK-OVER-WEEK ATTENDANCE ─────────────────────────────


$todayDate            = Carbon::today()->toDateString();
$todayWeekday         = Carbon::today()->format('l');
$lastWeekDate         = Carbon::today()->subWeek()->toDateString();
$lastWeekWeekday      = Carbon::today()->subWeek()->format('l');

$scheduleTodayCount = table::schedules()
    ->where('is_active', 1)
    ->whereDate('datefrom', '<=', $todayDate)
    ->whereDate('dateto',   '>=', $todayDate)
    ->whereRaw('NOT FIND_IN_SET(?, restday)', [$todayWeekday])
    ->pluck('reference')
    ->unique()
    ->count();
    $activeOnSchedule = table::schedules()
        ->where('is_active', 1)
        ->whereDate('datefrom', '<=', $todayDate)
        ->whereDate('dateto',   '>=', $todayDate)
        // strip spaces then ensure weekday NOT in restday CSV
        ->whereRaw("NOT FIND_IN_SET(?, REPLACE(restday, ' ', ''))", [$todayWeekday])
        ->distinct('reference')            // one count per person
        ->count('reference');

// 1) Count unique people who actually checked in today
$attendanceTodayCount = table::attendance()
    ->whereDate('date', $todayDate)
    ->pluck('reference')
    ->unique()
    ->count();

// 1) all attendance
$allattendance = table::attendance();

// 2) Compute percentage vs schedule
if ($scheduleTodayCount > 0) {
    $attendanceSchedulePct = round(
        ($attendanceTodayCount / $scheduleTodayCount) * 100,
        1
    );
} else {
    $attendanceSchedulePct = 0;
}

// 4) How many checked in that same weekday last week
$attendanceLastWeekCount = table::attendance()
    ->whereDate('date', $lastWeekDate)
    ->pluck('reference')
    ->unique()
    ->count();

// 5) Net increase in volunteers (today vs. last week)
$volunteerIncreaseCount = $attendanceTodayCount - $attendanceLastWeekCount;

// ───────────────────────────────────────────────────────────────────────────

$weeklyTotals = [];

    for ($i = 0; $i <= 12; $i++) {
        // Compute Monday–Sunday of “$i weeks ago”
        $start = Carbon::today()
            ->subWeeks($i)
            ->startOfWeek()
            ->toDateString();

        $end = Carbon::today()
            ->subWeeks($i)
            ->endOfWeek()
            ->toDateString();

        // Sum your precomputed totalhours for that range
        $hours = table::attendance()
            ->whereBetween('date', [$start, $end])
            ->sum('totalhours');

        $weeklyTotals[$i] = [
            'week_start' => $start,
            'week_end'   => $end,
            'hours'      => round($hours, 2),
        ];
    }

    // 2) Compute week-over-week change (this week vs. last week)
    $thisWeekHours = $weeklyTotals[0]['hours'];
    $lastWeekHours = $weeklyTotals[1]['hours'];

    // absolute difference
    $hoursDiff = $thisWeekHours - $lastWeekHours;

    // percentage change (guard divide-by-zero)
    $hoursPctChange = $lastWeekHours
        ? round(($hoursDiff / $lastWeekHours) * 100, 1)
        : 0;


// ────────────────────────────────────────────────────────────────

         // ── UNIQUE WEEKLY ATTENDANCE COUNTS ─────────────────────────────────────
    $weeklyAttendance = [];

    for ($i = 0; $i <= 12; $i++) {
        // Compute Monday–Sunday of “$i weeks ago”
        $start = Carbon::today()
            ->subWeeks($i)
            ->startOfWeek()
            ->toDateString();
        $end = Carbon::today()
            ->subWeeks($i)
            ->endOfWeek()
            ->toDateString();

        // Count unique reference in that range
        $count = table::attendance()
            ->whereBetween('date', [$start, $end])
            ->pluck('reference')       // get all reference values
            ->unique()            // drop duplicates
            ->count();            // count uniques

        $weeklyAttendance[$i] = [
            'week_start' => $start,
            'week_end'   => $end,
            'count'      => $count,
        ];
    }

    // 2) Compute week-over-week change (this week vs. last week)
    $thisWeekAtt = $weeklyAttendance[0]['count'];
    $lastWeekAtt = $weeklyAttendance[1]['count'];

    // absolute difference
    $countDiff = $thisWeekAtt - $lastWeekAtt;

    // percentage change (guard divide-by-zero)
    $countPctChange = $lastWeekAtt
        ? round(($countDiff / $lastWeekAtt) * 100, 1)
        : 0;

    // ──────────────────────────────────────────────────────────────────────────

         // 1) Determine Monday–Sunday for the current week
    $startOfWeek = Carbon::today()->startOfWeek()->toDateString();
    $endOfWeek   = Carbon::today()->endOfWeek()->toDateString();
    $todayWeekday = Carbon::today()->format('l');

    // 2) Count unique volunteers **scheduled** this week
    $scheduledThisWeek = table::schedules()
        ->where('is_active', 1)
        // the schedule must cover at least one day this week
        ->whereDate('datefrom', '<=', $endOfWeek)
        ->whereDate('dateto',   '>=', $startOfWeek)
        // strip spaces then ensure that at least one weekday in the week is NOT a restday
        // (this guarantees they were “on schedule” at some point this week)
        ->whereRaw("NOT FIND_IN_SET(?, REPLACE(restday,' ',''))", [$todayWeekday])
        ->pluck('reference')
        ->unique()
        ->count();

    // 3) Count unique volunteers who **actually attended** this week
    $attendanceThisWeek = table::attendance()
        ->whereBetween('date', [$startOfWeek, $endOfWeek])
        ->pluck('reference')
        ->unique()
        ->count();

    // 4) Compute % of scheduled who showed up
    $attendanceSchedulePctWeek = $scheduledThisWeek
        ? round(($attendanceThisWeek / $scheduledThisWeek) * 100, 1)
        : 0;

        // ──────────────────────────────────────────────────────────────────────────

     // … your existing dashboard logic …

    // 1) Define this week’s window and last week’s window
    $startThisWeek   = Carbon::today()->startOfWeek()->toDateString();
    $endThisWeek     = Carbon::today()->endOfWeek()->toDateString();
    $startLastWeek   = Carbon::today()->subWeek()->startOfWeek()->toDateString();
    $endLastWeek     = Carbon::today()->subWeek()->endOfWeek()->toDateString();

    // 2) Count unique attendees THIS week
    $currentWeekAttendees = table::attendance()
        ->whereBetween('date', [$startThisWeek, $endThisWeek])
        ->pluck('reference')
        ->unique()
        ->count();

    // 3) Count unique attendees LAST week
    $previousWeekAttendees = table::attendance()
        ->whereBetween('date', [$startLastWeek, $endLastWeek])
        ->pluck('reference')
        ->unique()
        ->count();

    // 4) Compute raw difference
    $attendanceDiff = $currentWeekAttendees - $previousWeekAttendees;

    // 5) Compute % change (guard divide-by-zero)
    if ($previousWeekAttendees > 0) {
        $attendancePctChange = round(
            ($attendanceDiff / $previousWeekAttendees) * 100,
            1
        );
    } elseif ($currentWeekAttendees > 0) {
        // from 0 → some, treat as 100% growth
        $attendancePctChange = 100;
    } else {
        $attendancePctChange = 0;
    }
// compute this‐ vs last‐week attendance %
    $attendanceDiff         = $currentWeekAttendees - $previousWeekAttendees;
    $attendancePctChange    = $previousWeekAttendees
        ? round(($attendanceDiff / $previousWeekAttendees) * 100, 1)
        : ($currentWeekAttendees ? 100 : 0);


     // ──────────────────────────────────────────────────────────────────────────

       // ── PRELOAD GENDER EMOJI LISTS ─────────────────────────────────────────
       // 1) Scan your on‐disk generic avatars
        //
        $avatarBase = public_path('assets2/images/avatars');
        $genericAvatars = [
            'male'   => collect(File::files("$avatarBase/male"))
                            ->map->getFilename()
                            ->all(),
            'female' => collect(File::files("$avatarBase/female"))
                            ->map->getFilename()
                            ->all(),
        ];

        //
        // 2) Subquery: each reference’s latest timein where timeout is blank/NULL/0000…
        //
        $recentSub = DB::table('tbl_people_attendance')
            ->select('reference', DB::raw('MAX(timein) as max_timein'))
            ->where(function($q) {
                $q->whereNull('timeout')
                  ->orWhere('timeout', '')
                  ->orWhere('timeout', '0000-00-00 00:00:00');
            })
            ->groupBy('reference');

        //
        // 3) Join it back, then link reference → people → campus_data
        //
        $recentAttendees = DB::table('tbl_people_attendance AS pa')
            // only the latest check-in rows
            ->joinSub($recentSub, 'recent', function($join) {
                $join->on('pa.reference', '=', 'recent.reference')
                     ->on('pa.timein', '=', 'recent.max_timein');
            })
            // still ensure no timeout
            ->where(function($q) {
                $q->whereNull('pa.timeout')
                  ->orWhere('pa.timeout', '')
                  ->orWhere('pa.timeout', '0000-00-00 00:00:00');
            })
            // 3a) link to people via pa.reference
            ->leftJoin('tbl_people AS p', 'pa.reference', '=', 'p.id')
            // 3b) link to campus data via p.id
            ->leftJoin('tbl_campus_data AS cd', 'cd.reference', '=', 'p.id')
            ->select([
                'pa.reference',
                'p.firstname',
                'p.lastname',
                'p.avatar',           // uploaded filename
                'p.gender',
                'cd.ministry',
                DB::raw('pa.timein as last_checkin'),
            ])
            ->orderByDesc('pa.timein')
            ->limit(8)
            ->get()
            // 4) Build each attendee’s avatar_url + ministry fallback
            ->map(function($att) use ($genericAvatars) {
                // a) If they uploaded a photo, use it:
                $faceRel  = "assets2/images/faces/{$att->avatar}";
                $facePath = public_path($faceRel);

                if ($att->avatar && file_exists($facePath)) {
                    $att->avatar_url = asset($faceRel);
                } else {
                    // b) Otherwise pick a random generic by gender
                    $gkey = Str::lower($att->gender ?: 'male');
                    $list = $genericAvatars[$gkey] ?? [];
                    $file = count($list) ? $list[array_rand($list)] : 'default.png';
                    $att->avatar_url = asset("assets2/images/avatars/{$gkey}/{$file}");
                }

                // c) Name fallback: if people record missing, show their reference
                if (! $att->firstname) {
                    $att->firstname = 'ID#' . $att->reference;
                    $att->lastname  = '';
                }

                // d) ministry fallback
                $att->ministry = $att->ministry ?: 'N/A';

                return $att;
            });

        // ────────────────────────────────────────────────────────────────────────

             // 1) Compute each user’s total hours, find the maximum (if you still need it)
    $maxHours = table::attendance()
        ->select(DB::raw('SUM(totalhours) as sumh'))
        ->groupBy('reference')
        ->orderByDesc('sumh')
        ->value('sumh') ?: 0;
        // 1) Look up current user's reference from campus_data
$myIdNo = table::campusdata()
    ->where('reference', Auth::id())
    ->value('reference');

    // 2) Compute the current user’s total hours
    $myHours = table::attendance()
    ->where('reference', $myIdNo)
    ->sum('totalhours');

    // 3) Define your level thresholds (levels 1–13)
    //    “Level 1” requires >= 16900 hours, down to “Level 13” for >= 1300
    $levelThresholds = [
        1  => 1500,
        2  => 1378,
        3  => 1257,
        4  => 1135,
        5  => 1013,
        6  =>  892,
        7  =>  770,
        8  =>  648,
        9  =>  527,
        10 =>  405,
        11 =>  283,
        12 =>  162,
        13 =>   40,
    ];

    // 4) Figure out which level you fall into
    //    Default to the lowest level (13)
    $bucket = 13;
    foreach ($levelThresholds as $lvl => $threshold) {
        if ($myHours >= $threshold) {
            $bucket = $lvl;
            break;
        }
    }

    // 5) Pick the image name for that level
    $welcomeImage = "welcome-back-{$bucket}.png";


        // ────────────────────────────────────────────────────────────────────────

$clicksByMonth = [];
$viewsByMonth  = [];

for ($i = 0; $i <= 8; $i++) {
    // YYYY-MM-DD for first & last day of the month $i ago
    $start = Carbon::now()->subMonths($i)->startOfMonth()->toDateString();
    $end   = Carbon::now()->subMonths($i)->endOfMonth()->toDateString();

    // 1) Total Clicks in that month
    $clicksByMonth[$i] = DB::table('user_activity_logs')
        ->whereNotNull('action')
        ->where('action', '<>', '')
        ->whereDate('action_time', '>=', $start)
        ->whereDate('action_time', '<=', $end)
        ->count();

    // 2) Unique-session Page Views in that month
    $viewsByMonth[$i] = DB::table('user_activity_logs')
        ->where('action', 'Page View')
        ->whereBetween('action_time', [$start, $end])
        ->distinct('session_id')
        ->count('session_id');
}

// Month-over-month growth (0=this month, 1=last month)
$clickGrowth = $clicksByMonth[1]
    ? round((($clicksByMonth[0] - $clicksByMonth[1]) / $clicksByMonth[1]) * 100, 1)
    : 0;

$viewGrowth = $viewsByMonth[1]
    ? round((($viewsByMonth[0] - $viewsByMonth[1]) / $viewsByMonth[1]) * 100, 1)
    : 0;

 // ────────────────────────────────────────────────────────────────────────

// 1) Get all active member ID numbers and count them
        $allMemberIdnos = DB::table('tbl_campus_data')
            ->pluck('reference')
            ->toArray();
        $totalMembers = count($allMemberIdnos);

        // 2) Prepare per-cycle arrays (cycle 0 = current Saturday–Friday, 1 = prior, … up to 8)
        $devotionCompletionPercentByCycle   = [];
        $devotionZeroEntryCountByCycle      = [];
        $devotionSixPlusEntryCountByCycle   = [];
        $devotionFullEntryCountByCycle      = [];
        $devotionCycleDates                 = []; // for reference if needed

        for ($cycle = 0; $cycle <= 8; $cycle++) {
            // 2a) Determine the Saturday–Friday window for this devotion cycle
            $cycleStartSaturday = Carbon::today()
                ->startOfWeek(Carbon::SATURDAY)
                ->subWeeks($cycle)
                ->toDateString();
            $cycleEndFriday     = Carbon::today()
                ->startOfWeek(Carbon::SATURDAY)
                ->subWeeks($cycle)
                ->addDays(6)
                ->toDateString();

            // store the window
            $devotionCycleDates[$cycle] = [
                'from' => $cycleStartSaturday,
                'to'   => $cycleEndFriday,
            ];

            // 2b) Count entries per member in that window
            $entriesPerMember = DB::table('tbl_people_devotion')
                ->whereBetween('devotion_date', [$cycleStartSaturday, $cycleEndFriday])
                ->select('reference', DB::raw('COUNT(*) as total'))
                ->groupBy('reference')
                ->pluck('total', 'reference')
                ->toArray();

            // 2c) Compute total entries and required total (members × 7)
            $totalDevotionEntries = array_sum($entriesPerMember);
            $requiredEntries      = $totalMembers * 7;

            // 2d) Percent of required entries completed
            $devotionCompletionPercentByCycle[$cycle] = $requiredEntries
                ? round($totalDevotionEntries / $requiredEntries * 100, 1)
                : 0;

            // 2e) How many members submitted 0, ≥6, and full 7 entries
            $devotionZeroEntryCountByCycle[$cycle]    = $totalMembers - count($entriesPerMember);
            $devotionSixPlusEntryCountByCycle[$cycle] = collect($entriesPerMember)
                ->filter(fn($count) => $count >= 6)
                ->count();
            $devotionFullEntryCountByCycle[$cycle]    = collect($entriesPerMember)
                ->filter(fn($count) => $count >= 7)
                ->count();
        }

        // 3) Extract "current cycle" (cycle 0) stats
        $currentDevotionPercent     = $devotionCompletionPercentByCycle[0];
        $currentZeroEntryCount      = $devotionZeroEntryCountByCycle[0];
        $currentSixPlusEntryCount   = $devotionSixPlusEntryCountByCycle[0];
        $currentFullEntryCount      = $devotionFullEntryCountByCycle[0];

        // 3b) Compute percentages (of total members) for the current cycle:
        $currentZeroPercent    = $totalMembers
            ? round($currentZeroEntryCount    / $totalMembers * 100, 1)
            : 0;
        $currentSixPlusPercent = $totalMembers
            ? round($currentSixPlusEntryCount / $totalMembers * 100, 1)
            : 0;
        $currentFullPercent    = $totalMembers
            ? round($currentFullEntryCount    / $totalMembers * 100, 1)
            : 0;

        // 3c) Growth of overall completion % from last cycle (1) → this cycle (0):
        $lastCyclePercent = $devotionCompletionPercentByCycle[1];
        $thisCyclePercent = $devotionCompletionPercentByCycle[0];

        $completionGrowth = $lastCyclePercent
            ? round((($thisCyclePercent - $lastCyclePercent) / $lastCyclePercent) * 100, 1)
            : 0;


            // ────────────────────────────────────────────────────────────────────────

// 1) Get all distinct, non-null, non-"web" computer names ever recorded
        $computers = table::attendance()
            ->whereNotNull('computer_name')
            ->where('computer_name', '<>', 'web')
            ->distinct()
            ->pluck('computer_name');

        // 2) Build status entries for each machine
        $computerStatuses = $computers->map(function($name) {
            // Grab the very latest attendance row for this machine
            $latest = table::attendance()
                ->where('computer_name', $name)
                ->orderBy('timein', 'desc')
                ->first();

            // If it has no totalhours (NULL, empty, or "."), it's active
            $isActive = $latest
                && (is_null($latest->totalhours)
                    || $latest->totalhours === ''
                    || $latest->totalhours === '.');

            if ($isActive) {
                return [
                    'computer' => $name,
                    'status'   => 'active',
                    'user'     => $latest->employee,
                ];
            }

            // Otherwise find the last row that *does* have real totalhours
            $last = table::attendance()
                ->where('computer_name', $name)
                ->whereNotNull('totalhours')
                ->where('totalhours', '<>', '')
                ->where('totalhours', '<>', '.')
                ->orderBy('timein', 'desc')
                ->first();

            return [
                'computer' => $name,
                'status'   => 'offline',
                'user'     => $last ? $last->employee : 'Unknown',
            ];
        })
        // 3) Sort active first
        ->sortByDesc(fn($c) => $c['status'] === 'active')
        ->values(); // reindex

// ────────────────────────────────────────────────────────────────────────

        // ── Volunteer (Star) of the Week ────────────────────────────────────────

        $weekStart = Carbon::today()
            ->startOfWeek(Carbon::SATURDAY)
            ->subWeek()
            ->startOfDay();
        $weekEnd = $weekStart->copy()
            ->addDays(6)
            ->endOfDay();

        // 2) Fixed goals
        $goalDevotions = 7;
        $goalMeetings  = 2;
        $goalHours     = 20;

        // 3) Raw‐count subqueries keyed by reference
        $devotions = DB::table('tbl_people_devotion')
            ->select('reference', DB::raw('COUNT(*) AS cnt'))
            ->whereBetween('devotion_date', [$weekStart, $weekEnd])
            ->groupBy('reference');

        $meetings = DB::table('meeting_attendance')
            ->select('reference', DB::raw('COUNT(*) AS cnt'))
            ->whereBetween('meeting_Date', [$weekStart, $weekEnd])
            ->groupBy('reference');

        $hours = DB::table('tbl_people_attendance')
            ->select('reference', DB::raw('SUM(totalhours) AS cnt'))
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->groupBy('reference');

        // 4) Main query: join people → campus_data → subqueries on p.id (reference)
        $star = table::people()
            ->from('tbl_people AS p')
            ->select(
                'p.id',
                'p.firstname',
                'p.lastname',
                'p.avatar',           
                'p.gender',
                'cd.campus    AS campus',
                'cd.ministry AS ministry',

                // raw counts
                DB::raw('COALESCE(dev.cnt,0) AS actual_devotions'),
                DB::raw('COALESCE(mt.cnt,0)  AS actual_meetings'),
                DB::raw('COALESCE(hr.cnt,0)  AS actual_hours'),

                // percentages of goals
                DB::raw("(COALESCE(dev.cnt,0)/{$goalDevotions}*100) AS pct_devotions"),
                DB::raw("(COALESCE(mt.cnt,0) /{$goalMeetings} *100) AS pct_meetings"),
                DB::raw("(COALESCE(hr.cnt,0) /{$goalHours}   *100) AS pct_hours"),

                // summed total percentage
                DB::raw("(
                    COALESCE(dev.cnt,0)/{$goalDevotions}
                  + COALESCE(mt.cnt,0) /{$goalMeetings}
                  + COALESCE(hr.cnt,0) /{$goalHours}
                ) * 100 AS total_percent")
            )
            // 4a) join to get each person’s campus/ministry
            ->leftJoin('tbl_campus_data AS cd', 'p.id', '=', 'cd.reference')
            // 4b) join counts on p.id (reference)
            ->leftJoinSub($devotions, 'dev', fn($j) => $j->on('dev.reference','p.id'))
            ->leftJoinSub($meetings,'mt',  fn($j) => $j->on('mt.reference','p.id'))
            ->leftJoinSub($hours,   'hr',  fn($j) => $j->on('hr.reference','p.id'))
            // 5) pick the highest total_percent
            ->orderByDesc('total_percent')
            ->first();
            // 1) Prepare the generic avatars lists (as you already do for recent attendees)
            $avatarBase = public_path('assets2/images/avatars');
            $genericAvatars = [
                'male'   => collect(File::files("$avatarBase/male"))
                                ->map->getFilename()
                                ->all(),
                'female' => collect(File::files("$avatarBase/female"))
                                ->map->getFilename()
                                ->all(),
            ];

            // 2) Compute avatar_url on the single $starOfWeek object
            if ($star) {
                // assume your table has an 'avatar' and 'gender' column on tbl_people
                $faceRel  = "assets2/images/faces/{$star->avatar}";
                $facePath = public_path($faceRel);

                if ($star->avatar && File::exists($facePath)) {
                    $star->avatar_url = asset($faceRel);
                } else {
                    // fallback to a random generic by gender
                    $gkey = Str::lower($star->gender ?: 'male');
                    $list = $genericAvatars[$gkey] ?? [];
                    $file = count($list) ? $list[array_rand($list)] : 'default.png';
                    $star->avatar_url = asset("assets2/images/avatars/{$gkey}/{$file}");
                }
            } else {
                // Create a default star object if no volunteer found
                $star = (object) [
                    'firstname' => 'No',
                    'lastname' => 'Volunteer',
                    'campus' => 'N/A',
                    'ministry' => 'N/A',
                    'actual_devotions' => 0,
                    'actual_hours' => 0,
                    'actual_meetings' => 0,
                    'avatar_url' => asset('assets2/images/avatars/default.png')
                ];
            }

        // ────────────────────────────────────────────────────────────────────────

        // Add missing variables for the new dashboard
        $dailyQrUrl = asset('assets2/qr/daily-qr.png'); // Default QR code URL
        $userAvatar = Auth::user()->avatar ?? null;
        $userGeneric = 'default.png'; // Default generic avatar
        
        return view('admin.index', compact(
            'tf',
            'emp_typeR',
            'emp_typeT',
            'emp_allActive',
            'emp_leaves_pending',
            'emp_leaves_approve',
            'emp_leaves_all',
            'emp_approved_leave',
            'emp_all_type',
            'star',
            'weekStart',
            'weekEnd' ,
            'recent_attendance',
            'is_online_now',
            'is_offline_now',
            'completed_tasks',
            'pending_tasks',
            'birthdaysToday',
            'users',
            'campuses',
            'ministries',
            // attendance‐overview vars
            'todayCount',
            'scheduleTodayCount',
            'activeOnSchedule',
            'attendanceSchedulePct',
            'growthVsLastWeek',
            'weeklyTotals',
            'hoursDiff',
            'hoursPctChange',
            'countPctChange',
            'volunteerIncreaseCount',
            'weeklyAttendance',
            'attendanceThisWeek',
            'scheduledThisWeek',
            'attendanceSchedulePctWeek',
            'currentWeekAttendees',
            'previousWeekAttendees',
            'attendanceDiff',
            'attendancePctChange',
            'recentAttendees',
            'welcomeImage',
            'maxHours',
            'computerStatuses',
            'bucket',
            'myHours',
            'clicksByMonth',
            'viewsByMonth',
            'clickGrowth',
            'viewGrowth',
            'totalMembers',
            'devotionCycleDates',
            'devotionCompletionPercentByCycle',
            'devotionZeroEntryCountByCycle',
            'devotionSixPlusEntryCountByCycle',
            'devotionFullEntryCountByCycle',
            'currentDevotionPercent',
            'currentZeroEntryCount',
            'currentSixPlusEntryCount',
            'currentFullEntryCount',
            'currentZeroPercent',
            'currentSixPlusPercent',
            'currentFullPercent',
            'completionGrowth',
            'attendancePercent',
            'dailyQrUrl',
            'userAvatar',
            'userGeneric'
        ));
    }

}
