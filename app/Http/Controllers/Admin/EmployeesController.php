<?php

namespace App\Http\Controllers\Admin;
use DB;
use App\Classes\table;
use App\Classes\permission;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EmployeesController extends Controller
{

public function index(Request $request)
{
    // ---- read filters
    $filterCampus     = $request->query('campus');
    $filterDepartment = $request->query('department');
    $filterMinistry   = $request->query('ministry');
    $status           = strtolower($request->query('status','all'));      // all|active|archived
    $activity         = strtolower($request->query('activity','all'));    // all|attendance|meeting

    // NEW: date range fields (preferred)
    $fromQ = $request->query('from');  // YYYY-MM-DD
    $toQ   = $request->query('to');    // YYYY-MM-DD

    // Back-compat: keep old param but only use if no from/to provided
    $activityWindow   = strtolower($request->query('activity_window','all')); // all|today|yesterday|7d|14d|1m|3m|custom

    // normalize
    if (!in_array($activity, ['all','attendance','meeting'])) $activity = 'all';
    if (!in_array($activityWindow, ['all','today','yesterday','7d','14d','1m','3m','custom'])) $activityWindow = 'all';

    // ---- subqueries by reference (always defined)
    $lastClockSub = DB::table('tbl_people_attendance')
        ->select('reference', DB::raw('MAX(timein) as last_clockin'))
        ->groupBy('reference');

    $lastMeetingSub = DB::table('meeting_attendance')
        ->select('reference', DB::raw('MAX(created_at) as last_meeting'))
        ->groupBy('reference');

    // ---- base + structural filters
    $base = table::people()
        ->leftJoin('tbl_campus_data as cd', 'tbl_people.id', '=', 'cd.reference');

    $filtered = (clone $base)
        ->when($filterCampus,     fn($q,$v)=>$q->where('cd.campus', $v))
        ->when($filterDepartment, fn($q,$v)=>$q->where('cd.department', $v))
        ->when($filterMinistry,   fn($q,$v)=>$q->where('cd.ministry', $v))
        ->when($status !== 'all', function($q) use ($status){
            if ($status === 'active') {
                $q->where('tbl_people.employmentstatus', 'Active');
            } else { // archived
                $q->whereIn('tbl_people.employmentstatus', ['Archive','Archived','Inactive']);
            }
        });

    // ---- ALWAYS define withActivity before any usage
    $withActivity = (clone $filtered)
        ->leftJoinSub($lastClockSub, 'lc', fn($j)=>$j->on('tbl_people.id','=','lc.reference'))
        ->leftJoinSub($lastMeetingSub, 'lm', fn($j)=>$j->on('tbl_people.id','=','lm.reference'));

    // ---- window calc (prefer from/to)
    $now = \Carbon\Carbon::now();
    $windowStart = null; $windowEnd = null;

    if ($fromQ || $toQ) {
        // Prefer explicit range
        if ($fromQ) $windowStart = \Carbon\Carbon::parse($fromQ)->startOfDay();
        if ($toQ)   $windowEnd   = \Carbon\Carbon::parse($toQ)->endOfDay();

        // If both set but reversed, swap
        if ($windowStart && $windowEnd && $windowStart->gt($windowEnd)) {
            [$windowStart, $windowEnd] = [$windowEnd, $windowStart];
        }
    } else {
        // Back-compat: fall back to preset windows
        switch ($activityWindow) {
            case 'today':
                $windowStart = $now->copy()->startOfDay(); $windowEnd = $now->copy()->endOfDay(); break;
            case 'yesterday':
                $windowStart = $now->copy()->subDay()->startOfDay(); $windowEnd = $now->copy()->subDay()->endOfDay(); break;
            case '7d':  $windowStart = $now->copy()->subDays(7);  $windowEnd = $now; break;
            case '14d': $windowStart = $now->copy()->subDays(14); $windowEnd = $now; break;
            case '1m':  $windowStart = $now->copy()->subMonth();  $windowEnd = $now; break;
            case '3m':  $windowStart = $now->copy()->subMonths(3);$windowEnd = $now; break;
            case 'custom':
            case 'all':
            default: /* leave nulls */ ;
        }
    }

    $ws = $windowStart? $windowStart->toDateTimeString() : null;
    $we = $windowEnd?   $windowEnd->toDateTimeString()   : null;

    // ---- scoped starts as withActivity (so it's ALWAYS defined)
    $scoped = (clone $withActivity);

    // apply window to scoped
    if ($ws !== null) {
        if ($activity === 'attendance') {
            $scoped = $scoped->whereNotNull('lc.last_clockin')
                             ->whereBetween('lc.last_clockin', [$ws, $we ?? \Carbon\Carbon::now()->toDateTimeString()]);
        } elseif ($activity === 'meeting') {
            $scoped = $scoped->whereNotNull('lm.last_meeting')
                             ->whereBetween('lm.last_meeting', [$ws, $we ?? \Carbon\Carbon::now()->toDateTimeString()]);
        } else {
            $end = $we ?? \Carbon\Carbon::now()->toDateTimeString();
            $scoped = $scoped
                ->where(function($q){
                    $q->whereNotNull('lc.last_clockin')->orWhereNotNull('lm.last_meeting');
                })
                ->whereRaw("
                    GREATEST(
                      IFNULL(lc.last_clockin,'1970-01-01 00:00:00'),
                      IFNULL(lm.last_meeting,'1970-01-01 00:00:00')
                    ) BETWEEN ? AND ?
                ", [$ws, $end]);
        }
    }

    // ---- build dataQuery from scoped (unchanged)
    if ($activity === 'attendance') {
        $dataQuery = (clone $scoped)
            ->when($ws === null, fn($q)=>$q->whereNotNull('lc.last_clockin'))
            ->addSelect([
                'tbl_people.id','cd.reference as reference','tbl_people.lastname','tbl_people.firstname','tbl_people.employmentstatus',
                'cd.idno','cd.campus','cd.department','cd.ministry',
                DB::raw("lc.last_clockin as last_activity"),
                DB::raw("'Clock-in' as last_activity_type"),
            ]);
    } elseif ($activity === 'meeting') {
        $dataQuery = (clone $scoped)
            ->when($ws === null, fn($q)=>$q->whereNotNull('lm.last_meeting'))
            ->addSelect([
                'tbl_people.id','cd.reference as reference','tbl_people.lastname','tbl_people.firstname','tbl_people.employmentstatus',
                'cd.idno','cd.campus','cd.department','cd.ministry',
                DB::raw("lm.last_meeting as last_activity"),
                DB::raw("'Meeting' as last_activity_type"),
            ]);
    } else {
        $dataQuery = (clone $scoped)
            ->addSelect([
                'tbl_people.id','cd.reference as reference','tbl_people.lastname','tbl_people.firstname','tbl_people.employmentstatus',
                'cd.idno','cd.campus','cd.department','cd.ministry',
                DB::raw("GREATEST(
                    IFNULL(lc.last_clockin,'1970-01-01 00:00:00'),
                    IFNULL(lm.last_meeting,'1970-01-01 00:00:00')
                ) as last_activity"),
                DB::raw("CASE
                    WHEN IFNULL(lc.last_clockin,'1970-01-01 00:00:00') >= IFNULL(lm.last_meeting,'1970-01-01 00:00:00')
                         AND lc.last_clockin IS NOT NULL THEN 'Clock-in'
                    WHEN lm.last_meeting IS NOT NULL THEN 'Meeting'
                    ELSE NULL
                END as last_activity_type"),
            ]);
    }

    $data = $dataQuery->orderBy('tbl_people.lastname')->get();

    // ---- counts (reuse same scope so cards match list)
    $countScope    = (clone $dataQuery);
    $countAll      = (clone $countScope)->count();
    $countActive   = (clone $countScope)->where('tbl_people.employmentstatus','Active')->count();
    $countArchived = (clone $countScope)->whereIn('tbl_people.employmentstatus',['Archive','Archived','Inactive'])->count();

    // ---- stale >14d (respect selected window if any)
    $twoWeeksAgo = \Carbon\Carbon::now()->subDays(14)->toDateTimeString();
    if ($activity === 'attendance') {
        $staleLastActivityCount = (clone $withActivity)
            ->whereNotNull('lc.last_clockin')
            ->when($ws !== null, fn($q)=>$q->whereBetween('lc.last_clockin', [$ws, $we ?? \Carbon\Carbon::now()->toDateTimeString()]))
            ->where('lc.last_clockin','<',$twoWeeksAgo)
            ->count();
    } elseif ($activity === 'meeting') {
        $staleLastActivityCount = (clone $withActivity)
            ->whereNotNull('lm.last_meeting')
            ->when($ws !== null, fn($q)=>$q->whereBetween('lm.last_meeting', [$ws, $we ?? \Carbon\Carbon::now()->toDateTimeString()]))
            ->where('lm.last_meeting','<',$twoWeeksAgo)
            ->count();
    } else {
        $end = $we ?? \Carbon\Carbon::now()->toDateTimeString();
        $staleLastActivityCount = (clone $withActivity)
            ->when($ws !== null, function($q) use ($ws,$end){
                $q->where(function($qq){
                    $qq->whereNotNull('lc.last_clockin')->orWhereNotNull('lm.last_meeting');
                })
                ->whereRaw("
                    GREATEST(
                      IFNULL(lc.last_clockin,'1970-01-01 00:00:00'),
                      IFNULL(lm.last_meeting,'1970-01-01 00:00:00')
                ) BETWEEN ? AND ?", [$ws,$end]);
            })
            ->where(function($q){
                $q->whereNotNull('lc.last_clockin')->orWhereNotNull('lm.last_meeting');
            })
            ->whereRaw("
                GREATEST(
                  IFNULL(lc.last_clockin,'1970-01-01 00:00:00'),
                  IFNULL(lm.last_meeting,'1970-01-01 00:00:00')
            ) < ?", [$twoWeeksAgo])
            ->count();
    }

    $memberSummary = [
        'all'      => $countAll,
        'active'   => $countActive,
        'archived' => $countArchived,
        'stale14d' => $staleLastActivityCount,
    ];

    // legacy top-line stats (unchanged)
    $emp_typeR   = (clone $countScope)->where('tbl_people.employmenttype','Regular')
                                      ->where('tbl_people.employmentstatus','Active')->count();
    $emp_typeT   = (clone $countScope)->where('tbl_people.employmenttype','Trainee')
                                      ->where('tbl_people.employmentstatus','Active')->count();
    $emp_genderM = (clone $countScope)->where('tbl_people.gender','Male')->count();
    $emp_genderR = (clone $countScope)->where('tbl_people.gender','Female')->count();

    $emp_allActive  = $countActive;
    $emp_allArchive = $countArchived;
    $emp_file       = $countAll;

    $campuses   = table::campus()->get();
    $ministries = table::ministry()->get();
    $department = table::department()->get();

    return view('admin.employees', compact(
        'data','memberSummary','filterCampus','filterDepartment','filterMinistry',
        'status','activity',
        // Note: 'activityWindow' no longer needed by the view after removing the select,
        // but you can pass it if other parts of the template still reference it.
        'campuses','ministries','department',
        'emp_allActive','emp_allArchive','emp_file','emp_typeR','emp_typeT','emp_genderM','emp_genderR'
    ));
}

	public function new() 
	{
		if (permission::permitted('employees-add')=='fail'){ return redirect()->route('denied'); }
		
		$employees = table::people()->get();
		$campus = table::campus()->get();
		$ministry = table::ministry()->get();
		$jobtitle = table::jobtitle()->get();
		$leavegroup = table::leavegroup()->get();
    	$department = table::department()->get();

	    return view('admin.new-employee', compact('campus', 'ministry', 'department', 'jobtitle', 'employees', 'leavegroup'));
	}
	
    public function add(Request $request)
{
    if (permission::permitted('employees-add') == 'fail') {
        return redirect()->route('denied');
    }

    $v = $request->validate([
        'lastname'         => 'required|alpha_dash_space|max:155',
        'firstname'        => 'required|alpha_dash_space|max:155',
        'emailaddress'     => 'required|email|max:155',
        'employmentstatus' => 'required|alpha_dash_space|max:155',
        // image is optional
        'image'            => 'nullable|image|max:3072',
        'image_cropped'    => 'nullable|string',
    ]);

    // ... your other field assignments (safe to leave nullables as null)
    $lastname      = mb_strtoupper($request->lastname);
    $firstname     = mb_strtoupper($request->firstname);
    $mi            = mb_strtoupper($request->mi ?? '');
    $gender        = mb_strtoupper($request->gender ?? '');
    $emailaddress  = mb_strtolower($request->emailaddress);
    $civilstatus   = mb_strtoupper($request->civilstatus ?? '');
    $temperament   = $request->temperament ?? null;
    $love_language = $request->love_language ?? null;
    $mobileno      = $request->mobileno ?? null;

    $birthday  = $request->filled('birthday') ? Carbon::parse($request->birthday)->format('Y-m-d') : null;
    $age       = $request->filled('birthday') ? Carbon::parse($request->birthday)->age : null;

    $nationalid   = mb_strtoupper($request->nationalid ?? '');
    $birthplace   = mb_strtoupper($request->birthplace ?? '');
    $homeaddress  = mb_strtoupper($request->homeaddress ?? '');
    $campus       = mb_strtoupper($request->campus ?? '');
    $ministry     = mb_strtoupper($request->ministry ?? '');
    $department   = mb_strtoupper($request->department ?? '');
    $jobposition  = mb_strtoupper($request->jobposition ?? '');
    $corporate_email = mb_strtolower($request->corporate_email ?? '');
    $leaveprivilege  = $request->leaveprivilege ?? null;

    // generate an idno that doesn't exist
    do {
        $idno    = (string) random_int(1000, 9999);
        $exists  = table::campusdata()->where('idno', $idno)->exists();
    } while ($exists);

    $employmenttype   = $request->employmenttype ?? null;
    $employmentstatus = $request->employmentstatus;
    $startdate        = $request->filled('startdate') ? Carbon::parse($request->startdate)->format('Y-m-d') : null;
    $dateregularized  = $request->filled('dateregularized') ? Carbon::parse($request->dateregularized)->format('Y-m-d') : null;

    // ---------- Avatar handling (prevents "undefined $name") ----------
    $avatarName = 'default.png'; // default if nothing uploaded

    if ($request->filled('image_cropped')) {
        $payload = $request->image_cropped;
        if (preg_match('/^data:image\/(png|jpeg|jpg|webp);base64,/', $payload, $m)) {
            $type   = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);
            $base64 = substr($payload, strpos($payload, ',') + 1);
            $binary = base64_decode($base64, true);
            // enforce max ~2MB
            if ($binary !== false && strlen($binary) <= 2 * 1024 * 1024) {
                $avatarName = 'avatar_'.time().'_'.Str::random(6).'.'.$type;
                $dest = public_path('assets2/faces/');
                if (!is_dir($dest)) { @mkdir($dest, 0755, true); }
                file_put_contents($dest.$avatarName, $binary);
            }
        }
    } elseif ($request->hasFile('image')) {
        $request->validate(['image' => 'image|mimes:jpg,jpeg,png,webp|max:2048']);
        $file = $request->file('image');
        $avatarName = 'avatar_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
        $dest = public_path('assets2/faces/');
        if (!is_dir($dest)) { @mkdir($dest, 0755, true); }
        $file->move($dest, $avatarName);
    }
    // ------------------------------------------------------------------

    table::people()->insert([[
        'lastname'        => $lastname,
        'firstname'       => $firstname,
        'mi'              => $mi,
        'age'             => $age,
        'gender'          => $gender,
        'emailaddress'    => $emailaddress,
        'civilstatus'     => $civilstatus,
        'temperament'     => $temperament,
        'love_language'   => $love_language,
        'mobileno'        => $mobileno,
        'birthday'        => $birthday,
        'birthplace'      => $birthplace,
        'nationalid'      => $nationalid,
        'homeaddress'     => $homeaddress,
        'employmenttype'  => $employmenttype,
        'employmentstatus'=> $employmentstatus,
        'avatar'          => $avatarName,   // << always defined now
    ]]);

    $refId = DB::getPdo()->lastInsertId();

    table::campusdata()->insert([[
        'reference'      => $refId,
        'campus'         => $campus,
        'ministry'       => $ministry,
        'department'     => $department,
        'jobposition'    => $jobposition,
        'corporate_email'=> $corporate_email,
        'leaveprivilege' => $leaveprivilege,
        'idno'           => $idno,
        'startdate'      => $startdate,
        'dateregularized'=> $dateregularized,
    ]]);

    return redirect('employees')->with('success', __("New volunteer has been added!"));
}
}
