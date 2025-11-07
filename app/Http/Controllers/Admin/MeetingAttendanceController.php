<?php
// app/Http/Controllers/Admin/MeetingAttendanceController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MeetingAttendanceController extends Controller
{
    /**
     * Unified resolver for GET (show page) + POST (store attendance)
     * Route:
     * Route::match(['get','post'], 'meeting-attendance/{token}', [MeetingAttendanceController::class, 'resolve'])
     *   ->where('token','[A-Za-z0-9\-_]+')
     *   ->name('meeting.attendance.resolve');
     */
    public function resolve(Request $request, string $token)
    {
        // 1) Fetch meeting link
        $link = DB::table('meeting_link')->where('token', $token)->first();
        if (!$link) {
            return back()->with('error', 'Invalid or unknown meeting link.');
        }

        // 2) Check expiry
        if (!empty($link->expires_at) && Carbon::parse($link->expires_at)->isPast()) {
            return back()->with('error', 'This meeting link has expired.');
        }
        // inside resolve() just before return view(...) for GET
        $campusOptions   = DB::table('tbl_form_campus')->orderBy('campus')->get(['id','campus']);
        $ministryOptions = DB::table('tbl_form_ministry')->orderBy('ministry')->get(['id','ministry']);
        $deptOptions     = DB::table('tbl_form_department')->orderBy('department')->get(['id','department']);

        // 3) GET → dynamic page (title/desc/mode)
        if ($request->isMethod('get')) {
            return view('attendance.dynamic-form', [
                'link'  => $link,
                'token' => $token,
                'campusOptions'   => $campusOptions,
                'ministryOptions' => $ministryOptions,
                'deptOptions'     => $deptOptions,
            ]);
        }

        // 4) POST → store in SAME TABLE (users + guests)
        $meetingId  = $link->id;              // use meeting_link.id as meeting_id
        $meetingDay = now()->toDateString();  // your meeting_date is DATE
        $mode       = $link->mode ?? 'auto';  // 'auto' or 'form'

        // ---------------- AUTO MODE (must be logged in) ----------------
        if ($mode === 'auto') {
            $user = $request->user();
            if (!$user) return back()->with('error', 'Please log in to check in.');

            // Eligibility checks (campus/ministry/department)
            $norm = function ($s) {
                $s = (string)$s;
                $s = trim($s);
                $s = preg_replace('/[\s_]+/u', ' ', $s);
                return mb_strtolower($s, 'UTF-8');
            };
            $inGroup = function ($value, array $group) use ($norm) {
                if (empty($group)) return true;
                $groupNorm = array_map($norm, $group);
                $pieces = preg_split('/[;,]/', (string)$value) ?: [];
                $pieces = $pieces ? array_map('trim', $pieces) : [(string)$value];
                foreach ($pieces as $p) {
                    if (in_array($norm($p), $groupNorm, true)) return true;
                }
                return false;
            };
            $toStrings = fn($arr) => array_values(array_filter(array_map(fn($v) => trim((string)$v), (array)$arr)));

            $campusData   = DB::table('tbl_campus_data')->where('reference', $user->reference ?? $user->id)->first();
            $userCampus   = $user->campus     ?? optional($campusData)->campus;
            $userMinistry = $user->ministry   ?? optional($campusData)->ministry;
            $userDept     = $user->department ?? optional($campusData)->department;

            $campusGroup   = $toStrings($link->campus_group   ? json_decode($link->campus_group, true)   : []);
            $ministryGroup = $toStrings($link->ministry_group ? json_decode($link->ministry_group, true) : []);
            $deptGroup     = $toStrings($link->dept_group     ? json_decode($link->dept_group, true)     : []);

            if (!$inGroup($userCampus,   $campusGroup))   return back()->with('error', 'This link is restricted to selected campuses.');
            if (!$inGroup($userMinistry, $ministryGroup)) return back()->with('error', 'This link is restricted to selected ministries.');
            if (!$inGroup($userDept,     $deptGroup))     return back()->with('error', 'This link is restricted to selected departments.');

            $duplicateMessage = 'Looks like you already checked in for this meeting today.';

            $existingAuto = DB::table('meeting_attendance')
                ->where('meeting_id', $meetingId)
                ->where('meeting_date', $meetingDay)
                ->where('user_id', $user->id)
                ->first();

            if ($existingAuto && !empty($existingAuto->checked_in_at)) {
                return back()->with('info', $duplicateMessage);
            }

            try {
                // Idempotent write: (meeting_id, meeting_date, user_id)
                DB::table('meeting_attendance')->updateOrInsert(
                    [
                        'meeting_id'   => $meetingId,
                        'meeting_date' => $meetingDay,
                        'user_id'      => $user->id,
                    ],
                    [
                        'token'        => $token,
                        'idno'         => $user->idno ?? null,
                        'employee'     => $user->name ?? $user->email,  // unified display name
                        'meeting'      => $link->title,
                        'description'  => $link->description ?? null,
                        'campus'       => $userCampus,
                        'ministry'     => $userMinistry,
                        'dept'         => $userDept,
                        'checked_in_at'=> now(),
                        'created_at'   => now(),
                        'updated_at'   => now(),
                        'source_mode'  => 'auto', // if column exists
                    ]
                );
            } catch (QueryException $e) {
                if ($this->isDuplicateAttempt($e)) {
                    return back()->with('info', $duplicateMessage);
                }

                throw $e;
            }

            return back()->with('success', 'Attendance Confirmed ✅');
        } 

        // ---------------- FORM MODE (public) ----------------
        $request->validate([
            'email'    => ['required','email:rfc,dns','max:190'],
            'employee' => ['nullable','string','max:190'], // single field (no first/last)
        ]);

        $email     = trim(strtolower($request->email));
        $emailNorm = $this->normalizeEmail($email);

        // If email belongs to a platform user → treat as user row
        $user = DB::table('users')->whereRaw('LOWER(email)=?', [$email])->first();
        if ($user) {
            $campusData   = DB::table('tbl_campus_data')->where('reference', $user->reference ?? $user->id)->first();
            $userCampus   = $user->campus     ?? optional($campusData)->campus;
            $userMinistry = $user->ministry   ?? optional($campusData)->ministry;
            $userDept     = $user->department ?? optional($campusData)->department;

            $duplicateMessage = 'Looks like you already checked in for this meeting today.';

            $existingUser = DB::table('meeting_attendance')
                ->where('meeting_id', $meetingId)
                ->where('meeting_date', $meetingDay)
                ->where('user_id', $user->id)
                ->first();

            if ($existingUser && !empty($existingUser->checked_in_at)) {
                return back()->with('info', $duplicateMessage);
            }

            try {
                DB::table('meeting_attendance')->updateOrInsert(
                    [
                        'meeting_id'   => $meetingId,
                        'meeting_date' => $meetingDay,
                        'user_id'      => $user->id,
                    ],
                    [
                        'token'        => $token,
                        'idno'         => $user->idno ?? null,
                        'employee'     => $user->name ?? $email, // still the unified display name
                        'meeting'      => $link->title,
                        'description'  => $link->description ?? null,
                        'campus'       => $userCampus,
                        'ministry'     => $userMinistry,
                        'dept'         => $userDept,
                        'checked_in_at'=> now(),
                        'created_at'   => now(),
                        'updated_at'   => now(),
                        'source_mode'  => 'form',
                    ]
                );
            } catch (QueryException $e) {
                if ($this->isDuplicateAttempt($e)) {
                    return back()->with('info', $duplicateMessage);
                }

                throw $e;
            }

            return back()->with('success', 'Attendance Confirmed ✅');
        }

        // Guest path (user_id = NULL). Progressive ask: reuse last "employee" for this email
        $previous = DB::table('meeting_attendance')
            ->whereNotNull('email_norm')
            ->where('email_norm', $emailNorm)
            ->orderByDesc('id')
            ->first();

        $employee = $previous->employee ?? ($request->employee ? mb_strtoupper($request->employee) : null);
        if (!$employee) {
            return back()->withInput()->with('error', 'Please add your name.');
        }

        // Idempotent for guests: (meeting_id, meeting_date, email_norm)
        $duplicateMessage = 'Looks like you already checked in for this meeting today.';

        $existingGuest = DB::table('meeting_attendance')
            ->where('meeting_id', $meetingId)
            ->where('meeting_date', $meetingDay)
            ->where('email_norm', $emailNorm)
            ->first();

        if ($existingGuest && !empty($existingGuest->checked_in_at)) {
            return back()->with('info', $duplicateMessage);
        }

        try {
            DB::table('meeting_attendance')->updateOrInsert(
                [
                    'meeting_id'   => $meetingId,
                    'meeting_date' => $meetingDay,
                    'email_norm'   => $emailNorm,
                ],
                [
                    'token'        => $token,
                    'email'        => $email,
                    'employee'     => $employee,          // unified display name for guests
                    'meeting'      => $link->title,
                    'description'  => $link->description ?? null,
                    'checked_in_at'=> now(),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                    'source_mode'  => 'form',
                ]
            );
        } catch (QueryException $e) {
            if ($this->isDuplicateAttempt($e)) {
                return back()->with('info', $duplicateMessage);
            }

            throw $e;
        }

        return back()->with('success', 'Attendance Confirmed ✅');
    }

    // ---------- Optional renderer kept for compatibility ----------
    private function renderResult($ok, $message, $token, $meeting, $already)
    {
        return view('attendance.confirm', [
            'ok'             => (bool) $ok,
            'message'        => $message,
            'meeting'        => $meeting ?: 'Meeting',
            'token'          => $token,
            'alreadyChecked' => (bool) $already,
        ]);
    }

    // ---------- Reports (unchanged except we skip NULL user_id for user KPIs) ----------
public function index(Request $request)
{
    // -------- Inputs
    $from      = $request->input('from') ?: now()->startOfMonth()->toDateString();
    $to        = $request->input('to')   ?: now()->endOfDay()->toDateTimeString();
    $type      = trim((string)$request->input('type', ''));               // if you use it
    $campus    = trim((string)$request->input('campus', ''));
    $ministry  = trim((string)$request->input('ministry', ''));
    $dept      = trim((string)$request->input('department', ''));
    $q         = trim((string)$request->input('q', ''));
    $meeting   = trim((string)$request->input('meeting', ''));            // NEW: meeting filter

    // Normalize dates
    $startDate = Carbon::parse($from)->startOfDay();
    $endDate   = Carbon::parse($to)->endOfDay();
    $daysSpan  = max($startDate->diffInDays($endDate) + 1, 1);

    // Options for selects
    $meetingOptions  = DB::table('meeting_attendance')->whereNotNull('meeting')->select('meeting')->distinct()->orderBy('meeting')->pluck('meeting');
    $campusOptions   = DB::table('meeting_attendance')->whereNotNull('campus')->distinct()->orderBy('campus')->pluck('campus');
    $ministryOptions = DB::table('meeting_attendance')->whereNotNull('ministry')->distinct()->orderBy('ministry')->pluck('ministry');
    $deptOptions     = DB::table('meeting_attendance')->whereNotNull('dept')->distinct()->orderBy('dept')->pluck('dept');

    // Optional columns
    $hasStatusCol = Schema::hasColumn('meeting_attendance', 'status');
    $hasLateCol   = Schema::hasColumn('meeting_attendance', 'is_late');

    // -------- Base scoped rows (used for table + metrics)
    $select = [
        'ma.employee','ma.campus','ma.dept','ma.ministry',
        'ma.meeting','ma.meeting_date','ma.created_at','ma.user_id',
    ];
    $select[] = $hasStatusCol ? 'ma.status' : DB::raw('NULL as status');

    $rows = DB::table('meeting_attendance as ma')
        ->select($select)
        ->when($startDate, fn($q) => $q->where('ma.meeting_date', '>=', $startDate->toDateString()))
        ->when($endDate,   fn($q) => $q->where('ma.meeting_date', '<=', $endDate->toDateString()))
        ->when($type !== '',    fn($q) => $q->where('ma.meeting', $type))    // keep if used
        ->when($meeting !== '', fn($q) => $q->where('ma.meeting', $meeting))
        ->when($campus !== '',  fn($q) => $q->where('ma.campus',  $campus))
        ->when($ministry !== '',fn($q) => $q->where('ma.ministry',$ministry))
        ->when($dept !== '',    fn($q) => $q->where('ma.dept',    $dept))
        ->when($q !== '', function($qq) use ($q) {
            $term = "%{$q}%";
            $qq->where(function($w) use ($term){
                $w->where('ma.employee','like',$term)
                  ->orWhere('ma.meeting','like',$term)
                  ->orWhere('ma.campus','like',$term)
                  ->orWhere('ma.ministry','like',$term)
                  ->orWhere('ma.dept','like',$term)
                  ->orWhere('ma.description','like',$term);
            });
        });

    // -------- People in scope (targeted)
    $eligibleUsers = $this->eligibleUsersQuery($campus, $ministry, $dept)->pluck('id');
    $targetedPeople = $eligibleUsers->count();

    // -------- Sessions span (distinct meeting occurrences)
    // Default = distinct (meeting, DATE(meeting_date))
    $sessionsSpan = (clone $rows)
        ->select(DB::raw('COUNT(DISTINCT CONCAT(IFNULL(ma.meeting,"?"), "|", DATE(ma.meeting_date))) as c'))
        ->value('c');
    $sessionsSpan = (int) ($sessionsSpan ?? 0);

    // -------- Totals
    // Count check-ins (restrict to targeted users if there are any; else count all)
    $totalCheckinsQ = (clone $rows);
    if ($targetedPeople > 0) {
        $totalCheckinsQ->whereIn('ma.user_id', $eligibleUsers);
    }
    $totalCheckins = (int) $totalCheckinsQ->count();

    // Distinct attendees (coverage)
    $uniqueAttendeesQ = (clone $rows)->whereNotNull('ma.user_id');
    if ($targetedPeople > 0) {
        $uniqueAttendeesQ->whereIn('ma.user_id', $eligibleUsers);
    }
    $uniqueAttendees = (int) $uniqueAttendeesQ->distinct('ma.user_id')->count('ma.user_id');

    // Per-person attended sessions (count distinct meeting-occurrences)
    $perPerson = (clone $rows)
        ->whereNotNull('ma.user_id')
        ->when($targetedPeople > 0, fn($q) => $q->whereIn('ma.user_id', $eligibleUsers))
        ->groupBy('ma.user_id')
        ->select('ma.user_id', DB::raw('COUNT(DISTINCT CONCAT(IFNULL(ma.meeting,"?"), "|", DATE(ma.meeting_date))) as sessions'))
        ->get();

    // Goal per person = attend all sessions in span (configurable)
    $targetSessions = $sessionsSpan; // or set a fixed target like min(5, $sessionsSpan)
    $metGoalPeople  = (int) $perPerson->where('sessions', '>=', $targetSessions)->count();

    // Helper
    $pct = fn($num, $den) => $den > 0 ? round(($num / $den) * 100) : 0;

    // Coverage
    $coveragePct = $pct($uniqueAttendees, max($targetedPeople, 1));

    // Consistency
    $consistencyPct = $pct($metGoalPeople, max($targetedPeople, 1));

    // Session strength
    $avgPerSession = $sessionsSpan > 0 ? round($totalCheckins / $sessionsSpan, 1) : 0;

    // Previous period (for delta on Session Strength)
    $prevEnd   = $startDate->copy()->subDay()->endOfDay();
    $prevStart = $prevEnd->copy()->subDays($daysSpan - 1)->startOfDay();

    $rowsPrev = DB::table('meeting_attendance as ma')
        ->when($type !== '',    fn($q) => $q->where('ma.meeting', $type))
        ->when($meeting !== '', fn($q) => $q->where('ma.meeting', $meeting))
        ->when($campus !== '',  fn($q) => $q->where('ma.campus',  $campus))
        ->when($ministry !== '',fn($q) => $q->where('ma.ministry',$ministry))
        ->when($dept !== '',    fn($q) => $q->where('ma.dept',    $dept))
        ->whereBetween('ma.meeting_date', [$prevStart->toDateString(), $prevEnd->toDateString()]);

    $sessionsSpanPrev = (int) ($rowsPrev
        ->select(DB::raw('COUNT(DISTINCT CONCAT(IFNULL(ma.meeting,"?"), "|", DATE(ma.meeting_date))) as c'))
        ->value('c') ?? 0);

    $totalCheckinsPrevQ = (clone $rowsPrev);
    if ($targetedPeople > 0) {
        $totalCheckinsPrevQ->whereIn('ma.user_id', $eligibleUsers);
    }
    $totalCheckinsPrev = (int) $totalCheckinsPrevQ->count();
    $avgPerSessionPrev = $sessionsSpanPrev > 0 ? round($totalCheckinsPrev / $sessionsSpanPrev, 1) : 0;

    $deltaPct = ($avgPerSessionPrev > 0)
        ? round((($avgPerSession - $avgPerSessionPrev) / $avgPerSessionPrev) * 100)
        : ($avgPerSession > 0 ? 100 : 0);

    // Goal progress (optional mini-bar)
    $checkinsGoal   = $targetedPeople * $sessionsSpan;
    $goalAchieved   = $pct($totalCheckins, max($checkinsGoal, 1)); // green percent
    $goalRemaining  = max($checkinsGoal - $totalCheckins, 0);
    $goalMissingPct = 100 - $goalAchieved;

    // -------- Summary payload for the 3 cards
    $summary = [
        // Card 1: Coverage
        'coveragePct'        => $coveragePct,             // big %
        'coverageCountsText' => "{$uniqueAttendees} of {$targetedPeople}",

        // Card 2: Consistency (met target sessions)
        'consistencyPct'     => $consistencyPct,          // big %
        'consistencyText'    => "Met: {$metGoalPeople} | Left: ".max($targetedPeople - $metGoalPeople, 0),
        'targetSessions'     => $targetSessions,

        // Card 3: Session Strength
        'avgPerSession'      => $avgPerSession,           // big number
        'strengthText'       => "Δ vs prev: ".($deltaPct >= 0 ? "▲ +{$deltaPct}%" : "▼ {$deltaPct}%"),
        'sessionGoal'        => $targetedPeople,          // optional capacity proxy
        'strengthPctOfGoal'  => $pct($avgPerSession, max($targetedPeople, 1)), // avg vs capacity

        // (Optional) Overall goal progress if you want a mini 4th bar
        'goalAchievedPct'    => $goalAchieved,
        'goalMissingPct'     => $goalMissingPct,
        'goalLeftToGo'       => $goalRemaining,

        // Context (debug/for tooltips)
        'sessionsSpan'       => $sessionsSpan,
        'daysSpan'           => $daysSpan,
        'targetedPeople'     => $targetedPeople,
    ];

    // -------- Table rows (paginate)
    $attendanceRows = (clone $rows)
        ->orderByDesc('ma.meeting_date')
        ->orderByDesc('ma.created_at')
        ->paginate(50);

    // -------- Return
    return view('admin.reports.meeting_attendance', compact(
        'from','to','type','campus','ministry','dept','q','meeting',
        'meetingOptions','campusOptions','ministryOptions','deptOptions',
        'attendanceRows','summary'
    ));
}

    private function isDuplicateAttempt(QueryException $e): bool
    {
        $sqlState = $e->getCode();
        $mysqlCode = $e->errorInfo[1] ?? null;
        $message = strtolower($e->getMessage());

        if ($sqlState === '23000' && in_array($mysqlCode, [1062, 1169, 1586], true)) {
            return true;
        }

        if ($sqlState === '22007' && str_contains($message, 'incorrect decimal value')) {
            return true;
        }

        if ($sqlState === '22007' && str_contains($message, 'invalid datetime format')) {
            return true;
        }

        return false;
    }
    public function expectedMissing(Request $request)
    {
        $campus   = trim($request->query('campus',   ''));
        $ministry = trim($request->query('ministry', ''));
        $dept     = trim($request->query('dept',     ''));
        $type     = trim($request->query('type',     ''));

        $today  = Carbon::today();
        $monday = (clone $today)->startOfWeek(Carbon::MONDAY);
        $sunday = (clone $today)->endOfWeek(Carbon::SUNDAY);

        $eligible = $this->eligibleUsersQuery($campus, $ministry, $dept)
            ->leftJoin('tbl_campus_data as cd', function($j){
                $j->on('cd.reference', '=', 'users.reference')
                  ->orOn('cd.reference', '=', 'users.id');
            })
            ->select(
                'users.id as user_id',
                'users.name',
                DB::raw('COALESCE(cd.campus, "") as campus'),
                DB::raw('COALESCE(cd.ministry, "") as ministry'),
                DB::raw('COALESCE(cd.department, "") as department')
            )
            ->orderBy('users.name')
            ->get();

        $presentThisWeek = DB::table('meeting_attendance')
            ->whereBetween('meeting_date', [$monday, $sunday])
            ->when($type     !== '', fn($q) => $q->where('meeting',  $type))
            ->when($campus   !== '', fn($q) => $q->where('campus',   $campus))
            ->when($ministry !== '', fn($q) => $q->where('ministry', $ministry))
            ->when($dept     !== '', fn($q) => $q->where('dept',     $dept))
            ->distinct()
            ->pluck('user_id');

        $missing = $eligible
            ->reject(fn($u) => $presentThisWeek->contains($u->user_id))
            ->values()
            ->map(fn($u) => [
                'id'         => (int) $u->user_id,
                'name'       => $u->name,
                'campus'     => $u->campus ?: '—',
                'ministry'   => $u->ministry ?: '—',
                'department' => $u->department ?: '—',
            ]);

        return response()->json([
            'window'  => ['from' => $monday->toDateString(), 'to' => $sunday->toDateString()],
            'filters' => compact('campus','ministry','dept','type'),
            'count'   => $missing->count(),
            'data'    => $missing,
        ]);
    }

    // -------- Helpers --------

    private function eligibleUsersQuery(string $campus = '', string $ministry = '', string $dept = '')
    {
        $hasCampusData = Schema::hasTable('tbl_campus_data');

        $q = DB::table('users')->select(
            'users.id as uid',
            'users.id as id',
            'users.name',
            'users.reference'
        );

        if ($hasCampusData) {
            $q->leftJoin('tbl_campus_data as cd', function($j){
                $j->on('cd.reference', '=', 'users.reference')
                  ->orOn('cd.reference', '=', 'users.id');
            });

            $q->when($campus   !== '', fn($w) => $w->where('cd.campus',     $campus));
            $q->when($ministry !== '', fn($w) => $w->where('cd.ministry',   $ministry));
            $q->when($dept     !== '', fn($w) => $w->where('cd.department', $dept));
        } else {
            $q->whereIn('users.id', function($sub){
                $sub->select('user_id')->from('meeting_attendance')->whereNotNull('user_id');
            });
        }

        return $q;
    }

    // In MeetingAttendanceController
public function suggest(Request $request)
{
    $q = trim((string)$request->query('email',''));
    if ($q === '') {
        return response()->json(['match'=>false,'profile'=>null,'suggestions'=>[]]);
    }

    $norm = fn(string $email) => $this->normalizeEmail(mb_strtolower(trim($email)));
    $email     = mb_strtolower($q);
    $emailNorm = $norm($email);

    // Exact match by normalized email in meeting_attendance or users
    $exact = DB::table('meeting_attendance')
        ->where('email_norm', $emailNorm)
        ->orderByDesc('checked_in_at')
        ->orderByDesc('updated_at')
        ->orderByDesc('id')
        ->first();

    $user = DB::table('users')->whereRaw('LOWER(email)=?', [$email])->first();
    if ($exact || $user) {
        $userCampus = $userMinistry = $userDept = null;
        if ($user) {
            $cd = DB::table('tbl_campus_data')->where('reference', $user->reference ?? $user->id)->first();
            $userCampus   = $user->campus     ?? optional($cd)->campus;
            $userMinistry = $user->ministry   ?? optional($cd)->ministry;
            $userDept     = $user->department ?? optional($cd)->department;
        }
        return response()->json([
            'match'  => true,
            'profile'=> [
                'email'    => $user->email ?? ($exact->email ?? $q),
                'employee' => $user->name  ?? ($exact->employee ?? null),
                'campus'   => $userCampus  ?? ($exact->campus ?? null),
                'ministry' => $userMinistry?? ($exact->ministry ?? null),
                'dept'     => $userDept    ?? ($exact->dept ?? null),
                'is_user'  => (bool)$user,
            ],
            'suggestions'=>[],
        ]);
    }

    // SUGGESTIONS: contains search for emails we’ve seen before
    $like = '%'.$email.'%';
    $suggestions = DB::table('meeting_attendance')
        ->select('email','employee','campus','ministry','dept')
        ->whereNotNull('email')
        ->whereRaw('LOWER(email) LIKE ?', [$like])
        ->groupBy('email','employee','campus','ministry','dept')
        ->orderByRaw('MAX(updated_at) DESC')
        ->limit(8)
        ->get();

    return response()->json([
        'match'       => false,
        'profile'     => null,
        'suggestions' => $suggestions,
    ]);
}
    private function buildHeatmapMatrix($heat)
    {
        $grid = [];
        for ($d=1;$d<=7;$d++){ $grid[$d] = array_fill(0,24,0); }
        foreach ($heat as $row){
            $mysqlDow = (int)$row->dow; // 1..7 Sun..Sat
            $hour     = (int)$row->hh;  // 0..23
            $count    = (int)$row->c;

            $uiDow = $mysqlDow === 1 ? 7 : $mysqlDow - 1; // 1..7 Mon..Sun
            $grid[$uiDow][$hour] = $count;
        }
        $names  = [1=>'Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $series = [];
        for ($d=1;$d<=7;$d++){
            $data = [];
            for ($h=0;$h<=23;$h++){
                $data[] = ['x' => sprintf('%02d:00',$h), 'y' => $grid[$d][$h]];
            }
            $series[] = ['name'=>$names[$d], 'data'=>$data];
        }
        return $series;
    }

    /**
     * Normalize emails so gmail aliases (dots / +tags) are treated as the same person.
     */
    private function normalizeEmail(string $email): string
    {
        $email = trim(mb_strtolower($email));
        if (!str_contains($email,'@')) return $email;
        [$local,$domain] = explode('@',$email,2);
        if (in_array($domain, ['gmail.com','googlemail.com'])) {
            $local = preg_replace('/\+.*/','', $local);
            $local = str_replace('.', '', $local);
            $domain = 'gmail.com';
        }
        return $local.'@'.$domain;
    }
}
