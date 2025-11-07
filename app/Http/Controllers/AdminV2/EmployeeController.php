<?php

namespace App\Http\Controllers\AdminV2;

use App\Classes\permission;
use DB;
use App\Http\Controllers\Controller;
use App\Models\Person;       // tbl_people
use App\Models\Department;   // tbl_form_department
use App\Models\JobTitle;     // tbl_form_jobtitle
use App\Models\Campus;       // tbl_campus_info (master)
use App\Models\CampusData;   // tbl_campus_data (assignment)
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (permission::permitted('employees') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['index', 'json']]);

        $this->middleware(function ($request, $next) {
            if (permission::permitted('employees-add') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['store']]);

        $this->middleware(function ($request, $next) {
            if (permission::permitted('employees-edit') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['update']]);

        $this->middleware(function ($request, $next) {
            if (permission::permitted('employees-delete') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['destroy']]);
    }

    // ============= PAGE (cards + initial table render) =============
    public function index(Request $request)
{
    [$from, $to] = $this->parseDateRange($request->input('date_range'));
    $status      = $request->input('status');       // active|inactive|null
    $jobtitleId  = $request->input('designation');  // id
    $campusId    = $request->input('campus_id');    // id (optional)
    $sort        = $request->input('sort', 'desc'); // asc|desc
    $search      = $request->input('q');

    // ---- Cards (current counts)
    $total    = Person::count();
    $active   = Person::whereIn('employmentstatus', ['Active', 1, '1'])->count();
    $inactive = $total - $active;
    $newJoiners = (!$from && !$to)
        ? Person::whereDate('created_at', '>=', now()->subDays(7))->count()
        : Person::when($from, fn($q)=>$q->whereDate('created_at','>=',$from))
                ->when($to,   fn($q)=>$q->whereDate('created_at','<=',$to))
                ->count();

    $cards = [
        'total'      => $total,
        'active'     => $active,
        'inactive'   => $inactive,
        'newJoiners' => $newJoiners,
        'trends'     => [
            'total'      => ['pct' => 0, 'dir' => 'up'],
            'active'     => ['pct' => 0, 'dir' => 'up'],
            'inactive'   => ['pct' => 0, 'dir' => 'up'],
            'newJoiners' => ['pct' => 0, 'dir' => 'up'],
        ],
    ];

    // ---- Build base list + LAST ACTIVITY (attendance/meeting)
    $lastClockSub = DB::table('tbl_people_attendance')
        ->select('idno', DB::raw('MAX(timein) as last_clockin'))
        ->groupBy('idno');

    $lastMeetingSub = DB::table('meeting_attendance')
        ->select('idno', DB::raw('MAX(created_at) as last_meeting'))
        ->groupBy('idno');

    // --- pick ONE campus_data row per person (latest by id; change to startdate if you prefer)
$cdLatest = DB::table('tbl_campus_data')
    ->select(DB::raw('MAX(id) as id'), 'reference')
    ->groupBy('reference');

// last-activity subqueries
$lastClockSub = DB::table('tbl_people_attendance')
    ->select('reference', DB::raw('MAX(timein) as last_clockin'))
    ->groupBy('reference');

$lastMeetingSub = DB::table('meeting_attendance')
    ->select('reference', DB::raw('MAX(created_at) as last_meeting'))
    ->groupBy('reference');

$people = Person::query()
    ->with(['department','jobTitle','currentCampusData.campus'])

    // join latest campus_data row per person
    ->leftJoinSub($cdLatest, 'cdl', function ($j) {
        $j->on('tbl_people.id', '=', 'cdl.reference');
    })
    ->leftJoin('tbl_campus_data as cd', 'cd.id', '=', 'cdl.id')

    // attach last activity (by reference)
    ->leftJoinSub($lastClockSub,   'lc', fn($j)=>$j->on('tbl_people.id','=','lc.reference'))
    ->leftJoinSub($lastMeetingSub, 'lm', fn($j)=>$j->on('tbl_people.id','=','lm.reference'))

    // select people.* so models hydrate, plus computed last-activity fields
    ->select([
        'tbl_people.*',
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
    ])

    // filters
    ->when($status, function ($q) use ($status) {
        return strtolower($status) === 'active'
            ? $q->whereIn('tbl_people.employmentstatus',['Active',1,'1'])
            : $q->whereNotIn('tbl_people.employmentstatus',['Active',1,'1']);
    })
    ->when($jobtitleId, fn($q)=>$q->where('tbl_people.jobtitle_id', $jobtitleId))
    ->when($campusId,   fn($q)=>$q->whereHas('campusData', fn($w)=>$w->where('campus_id',$campusId)))
    ->when($from,       fn($q)=>$q->whereDate('tbl_people.created_at','>=',$from))
    ->when($to,         fn($q)=>$q->whereDate('tbl_people.created_at','<=',$to))
    ->when($search, function ($q) use ($search) {
        $like = '%'.$search.'%';
        $q->where(function($w) use ($like) {
            $w->where('tbl_people.firstname','like',$like)
              ->orWhere('tbl_people.lastname','like',$like)
              ->orWhere('tbl_people.emailaddress','like',$like)
              ->orWhere('tbl_people.mobileno','like',$like);
        });
    })
    ->orderBy('tbl_people.created_at', $sort)
    ->get();

    // ---- Trends (same windows, computed on filtered base)
    $percent = function ($current, $previous) {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 2);
    };

    if ($from && $to) {
        $curStart = $from->copy()->startOfDay();
        $curEnd   = $to->copy()->endOfDay();
    } else {
        $curStart = now()->copy()->subDays(6)->startOfDay();
        $curEnd   = now()->endOfDay();
    }
    $days      = $curEnd->diffInDays($curStart);
    $prevEnd   = $curStart->copy()->subSecond();
    $prevStart = $prevEnd->copy()->subDays($days)->startOfDay();

    $filteredBase = Person::query()
        ->when($status, function ($q) use ($status) {
            $isActive = strtolower($status) === 'active';
            return $isActive
                ? $q->whereIn('employmentstatus',['Active',1,'1'])
                : $q->whereNotIn('employmentstatus',['Active',1,'1']);
        })
        ->when($jobtitleId, fn($q)=>$q->where('jobtitle_id', $jobtitleId))
        ->when($campusId,   fn($q)=>$q->whereHas('campusData', fn($w)=>$w->where('campus_id',$campusId)));

    $curTotalSnap   = (clone $filteredBase)->where('created_at','<=',$curEnd)->count();
    $prevTotalSnap  = (clone $filteredBase)->where('created_at','<=',$prevEnd)->count();
    $curActiveSnap  = (clone $filteredBase)->where('created_at','<=',$curEnd)->whereIn('employmentstatus',['Active',1,'1'])->count();
    $prevActiveSnap = (clone $filteredBase)->where('created_at','<=',$prevEnd)->whereIn('employmentstatus',['Active',1,'1'])->count();

    $curInactiveSnap  = $curTotalSnap  - $curActiveSnap;
    $prevInactiveSnap = $prevTotalSnap - $prevActiveSnap;

    $curJoiners  = (clone $filteredBase)->whereBetween('created_at', [$curStart, $curEnd])->count();
    $prevJoiners = (clone $filteredBase)->whereBetween('created_at', [$prevStart, $prevEnd])->count();

    $cards['trends'] = [
        'total'      => ['pct' => $percent($curTotalSnap,   $prevTotalSnap),   'dir' => $curTotalSnap   >= $prevTotalSnap   ? 'up' : 'down'],
        'active'     => ['pct' => $percent($curActiveSnap,  $prevActiveSnap),  'dir' => $curActiveSnap  >= $prevActiveSnap  ? 'up' : 'down'],
        'inactive'   => ['pct' => $percent($curInactiveSnap,$prevInactiveSnap),'dir' => $curInactiveSnap>= $prevInactiveSnap? 'up' : 'down'],
        'newJoiners' => ['pct' => $percent($curJoiners,     $prevJoiners),     'dir' => $curJoiners     >= $prevJoiners     ? 'up' : 'down'],
    ];

    // ---- Dropdowns
    $departments  = Department::orderBy('department')->get(['id','department']);
    $designations = JobTitle::orderBy('jobtitle')->get(['id','jobtitle']);
    $campuses     = Campus::orderBy('campus')->get(['id','campus']);

    return view('admin_v2.employees', [
        'cards'    => $cards,
        'filters'  => [
            'date_from'   => $from ? $from->format('Y-m-d') : null,
            'date_to'     => $to ? $to->format('Y-m-d') : null,
            'status'      => $status,
            'designation' => $jobtitleId,
            'campus_id'   => $campusId,
            'sort'        => $sort,
            'q'           => $search,
        ],
        'dropdowns' => compact('departments','designations','campuses'),
        'people'    => $people,
    ]);
}

    // ============= DATATABLE JSON (server-side) =============
    public function json(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request->input('date_range'));
        $status      = $request->input('status');
        $jobtitleId  = $request->input('designation');
        $campusId    = $request->input('campus_id');
        $sort        = $request->input('sort', 'desc');
        $search      = $request->input('q');

        $query = Person::with([
                'department',
                'jobTitle',
                'currentCampusData' => function ($q) { $q->withoutGlobalScopes(); },
                'currentCampusData.campus' => function ($q) { $q->withoutGlobalScopes(); },
            ])
            ->when($status, fn($q)=>strtolower($status)==='active'
                ? $q->whereIn('employmentstatus',['Active',1,'1'])
                : $q->whereNotIn('employmentstatus',['Active',1,'1'])
            )
            ->when($jobtitleId, fn($q)=>$q->where('jobtitle_id', $jobtitleId))
            ->when($campusId, fn($q)=>$q->whereHas('campusData', fn($w)=>$w->withoutGlobalScopes()->where('campus_id', $campusId)))
            ->when($from, fn($q)=>$q->whereDate('created_at','>=',$from))
            ->when($to,   fn($q)=>$q->whereDate('created_at','<=',$to))
            ->when($search, function ($q) use ($search) {
                $like = '%'.$search.'%';
                $q->where(function($w) use ($like) {
                    $w->where('firstname','like',$like)
                      ->orWhere('lastname','like',$like)
                      ->orWhere('emailaddress','like',$like)
                      ->orWhere('mobileno','like',$like);
                });
            })
            ->orderBy('created_at', $sort);

        $length = (int) $request->input('length', 10);
        $start  = (int) $request->input('start', 0);

        $total  = $query->count();
        $rows   = $query->skip($start)->take($length)->get();

        $data = $rows->map(function (Person $p) {
           return [
               'select_html'   => '<div class="form-check form-check-md"><input class="form-check-input row-check" type="checkbox" value="'.$p->id.'"></div>',
               'employee_code' => 'Emp-'.str_pad($p->id, 3, '0', STR_PAD_LEFT),
               'name_html'     => view('admin_v2.employees.partials.name_cell', ['p' => $p])->render(),
               'email'         => e($p->emailaddress),
               'phone'         => e($p->mobileno),
               'designation'   => e(optional($p->jobTitle)->job_title), // ✅ FIXED
               'joining_date'  => optional($p->created_at)->format('d M Y'),
               'status_html'   => view('admin_v2.employees.partials.status_badge', ['p' => $p])->render(),
               'actions_html'  => view('admin_v2.employees.partials.actions', ['p' => $p])->render(),
           ];
        });

        return response()->json([
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $data,
        ]);
    }

    // ============= CREATE (with optional campus assignment) =============
    public function store(Request $request)
    {
        $data = $request->validate([
            'firstname'      => ['required','string','max:100'],
            'lastname'       => ['nullable','string','max:100'],
            'emailaddress'   => ['required','email','max:150','unique:tbl_people,emailaddress'],
            'mobileno'       => ['required','string','max:50'],
            'employmentstatus'=> ['required'],

            // optional campus assignment (tbl_campus_data)
            'campus_id'     => ['nullable','integer','exists:tbl_campus_info,id'],

            // legacy-compatible optional fields
            'department'     => ['nullable','string','max:150'],
            'ministry'       => ['nullable','string','max:150'],
            'jobposition'    => ['nullable','string','max:150'],
            'corporate_email'=> ['nullable','string','max:150'],
            'idno'           => ['nullable','string','max:50'],
            'startdate'      => ['nullable','date'],
            'dateregularized'=> ['nullable','date'],
        ]);

        $person = Person::create($data);

        // upsert current campus assignment if provided
        if (!empty($data['campus_id'])) {
            CampusData::create([
                'reference'       => $person->id,         // FK to tbl_people.id
                'campus_id'       => $data['campus_id'],
                'department'      => $data['department'] ?? null,
                'ministry'        => $data['ministry'] ?? null,
                'jobposition'     => $data['jobposition'] ?? null,
                'corporate_email' => $data['corporate_email'] ?? null,
                'idno'            => $data['idno'] ?? null,
                'startdate'       => $data['startdate'] ?? null,
                'dateregularized' => $data['dateregularized'] ?? null,
            ]);
        }

        return response()->json(['ok' => true, 'id' => $person->id]);
    }

    // ============= UPDATE (with optional campus upsert) =============
    public function update(Request $request, Person $person)
    {
        $data = $request->validate([
            'firstname'      => ['required','string','max:100'],
            'lastname'       => ['nullable','string','max:100'],
            'emailaddress'   => ['required','email','max:150', Rule::unique('tbl_people','emailaddress')->ignore($person->id)],
            'mobileno'       => ['required','string','max:50'],
            'employmentstatus'=> ['required'],

            // optional campus assignment changes
            'campus_id'     => ['nullable','integer','exists:tbl_campus_info,id'],

            // legacy-compatible optional fields
            'department'     => ['nullable','string','max:150'],
            'ministry'       => ['nullable','string','max:150'],
            'jobposition'    => ['nullable','string','max:150'],
            'corporate_email'=> ['nullable','string','max:150'],
            'idno'           => ['nullable','string','max:50'],
            'startdate'      => ['nullable','date'],
            'dateregularized'=> ['nullable','date'],
        ]);

        $person->update($data);

        if (array_key_exists('campus_id', $data)) {
            $current = $person->currentCampusData()->first();
            $incomingCampusId = $data['campus_id'];

            if ($incomingCampusId && !$current) {
                CampusData::create([
                    'reference'       => $person->id,
                    'campus_id'       => $incomingCampusId,
                    'department'      => $data['department'] ?? null,
                    'ministry'        => $data['ministry'] ?? null,
                    'jobposition'     => $data['jobposition'] ?? null,
                    'corporate_email' => $data['corporate_email'] ?? null,
                    'idno'            => $data['idno'] ?? null,
                    'startdate'       => $data['startdate'] ?? null,
                    'dateregularized' => $data['dateregularized'] ?? null,
                ]);
            } elseif ($incomingCampusId && $current) {
                $current->update([
                    'campus_id'       => $incomingCampusId,
                    'department'      => $data['department'] ?? $current->department,
                    'ministry'        => $data['ministry'] ?? $current->ministry,
                    'jobposition'     => $data['jobposition'] ?? $current->jobposition,
                    'corporate_email' => $data['corporate_email'] ?? $current->corporate_email,
                    'idno'            => $data['idno'] ?? $current->idno,
                    'startdate'       => $data['startdate'] ?? $current->startdate,
                    'dateregularized' => $data['dateregularized'] ?? $current->dateregularized,
                ]);
            }
        }

        return response()->json(['ok' => true]);
    }

    // ============= DELETE =============
    public function destroy(Person $person)
    {
        // Optional: also clean related campus_data if you want cascading
        // CampusData::where('reference', $person->id)->delete();
        $person->delete();
        return response()->json(['ok' => true]);
    }

    // ============= Helpers =============
    private function parseDateRange(?string $range): array
    {
        if (!$range || !str_contains($range, '-')) return [null, null];
        [$fromRaw, $toRaw] = array_map('trim', explode('-', $range));
        try {
            $from = Carbon::createFromFormat('d/m/Y', $fromRaw)->startOfDay();
            $to   = Carbon::createFromFormat('d/m/Y', $toRaw)->endOfDay();
        } catch (\Throwable $e) { return [null, null]; }
        return [$from, $to];
    }
}
