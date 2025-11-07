<?php

namespace App\Http\Controllers\AdminV2;

use App\Classes\permission;
use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Ministry;
use App\Models\Person;
use App\Models\Schedule; // Eloquent pointing to tbl_people_schedules
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleTimingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (permission::permitted('schedules') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['index', 'showJson']]);

        $this->middleware(function ($request, $next) {
            if (permission::permitted('schedules-add') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['store']]);

        $this->middleware(function ($request, $next) {
            if (permission::permitted('schedules-edit') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['update', 'archive']]);

        $this->middleware(function ($request, $next) {
            if (permission::permitted('schedules-delete') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['destroy']]);
    }

    /** Show page + modal lists */
    public function index(Request $request)
    {
        // People list for the modal (just id + name)
        $people = Person::orderBy('lastname')
            ->orderBy('firstname')
            ->get(['id','firstname','lastname']);

        // Live schedules list for the table
        $schedules = DB::table('tbl_people_schedules as s')
            ->leftJoin('tbl_people as p', 'p.id', '=', 's.reference')
            ->select(
                's.id',
                's.reference',
                DB::raw("COALESCE(s.employee, CONCAT(UPPER(p.lastname), ', ', UPPER(p.firstname))) as employee_name"),
                's.department',
                's.ministry',
                's.datefrom',
                's.dateto',
                's.start_time',
                's.end_time',
                's.hours',
                's.restday',
                's.is_active',
                's.created_at'
            )
            ->where('s.archive', 0)
            ->orderByDesc('s.created_at')
            ->get();

        return view('admin_v2.schedule-timing', compact('people','schedules'));
    }

/** Small helpers you can paste at the bottom of the controller if you wish */
private function fmtTime(?string $t): string
{
    if (!$t) return '';
    try { return Carbon::createFromFormat('H:i:s', $t)->format('h:i A'); }
    catch (\Throwable $e) { return $t; }
}
private function fmtDate(?string $d): string
{
    if (!$d) return '';
    try { return Carbon::parse($d)->format('d-m-Y'); }
    catch (\Throwable $e) { return $d; }
}

    /** Save a schedule row */
    public function store(Request $request)
    {
        // 1) Validate
        $data = $request->validate([
            'employee_reference' => ['required','integer','exists:tbl_people,id'],

            // dates can be dd/mm/yyyy, mm/dd/yyyy, yyyy-mm-dd — we'll normalize
            'datefrom'           => ['nullable','string'],
            'dateto'             => ['nullable','string'],

            // planned times
            'start_time'         => ['required','string'],
            'end_time'           => ['required','string'],
            'max_start_time'     => ['required','string'],
            'max_end_time'       => ['required','string'],

            // multi rest day
            'restday'            => ['nullable','array'],
            'restday.*'          => ['string'],

            'notes'              => ['nullable','string'],
            'is_active'          => ['nullable','boolean'],

            // optional manual override (we compute it anyway)
            'hours'              => ['nullable','string'],
        ]);

        // 2) Person & full name
        $person = Person::select('id','firstname','lastname')->findOrFail($data['employee_reference']);
        $employeeFullName = trim($person->lastname . ', ' . $person->firstname);

        // 3) Resolve org (latest assignment in campus table)
        $org = DB::table('tbl_campus_data')
            ->where('reference', $person->id)
            ->latest('id')
            ->first();

        $campus     = $org->campus     ?? null;
        $department = $org->department ?? null;
        $ministry   = $org->ministry   ?? null;

        // 4) Normalize dates to Y-m-d (columns can be DATE or VARCHAR)
        [$datefrom, $dateto] = $this->normalizeDateRange(
            $request->input('datefrom'),
            $request->input('dateto')
        );

        // 5) Normalize times to HH:MM:SS
        $toTime = function (?string $t) {
            if ($t === null || $t === '') return null;
            return date('H:i:s', strtotime($t));
        };

        $tStart    = $toTime($data['start_time']);
        $tEnd      = $toTime($data['end_time']);
        $tMaxStart = $toTime($data['max_start_time']);
        $tMaxEnd   = $toTime($data['max_end_time']);

        // 6) Rest days -> CSV (e.g., "Sunday,Saturday")
        $restDays   = $data['restday'] ?? [];
        $restdayCsv = implode(',', $restDays);

        // 7) Compute weekly hours for the range (excludes rest days)
        $hoursHHMM = $this->computeHoursHHMM($datefrom, $dateto, $tStart, $tEnd, $restDays);

        // 8) Build and insert
        $payload = [
            // core ids
            'reference'          => (string)$person->id,
            'employee'           => $employeeFullName,

            // planned times
            'start_time'         => $tStart,
            'end_time'           => $tEnd,
            'max_start_time'     => $tMaxStart,
            'max_end_time'       => $tMaxEnd,

            // date range
            'datefrom'           => $datefrom,
            'dateto'             => $dateto,

            // org (auto-resolved)
            'campus'             => $campus,
            'department'         => $department,
            'ministry'           => $ministry,

            // flags & notes
            'is_active'          => $request->boolean('is_active') ? 1 : 0,
            'archive'            => 0,
            'notes'              => $data['notes'] ?? null,

            // summary
            'hours'              => $hoursHHMM,   // varchar(20)
            'restday'            => $restdayCsv,  // varchar(100)
        ];

        // IMPORTANT: remove any dd()/dump() here so the insert actually runs
        Schedule::create($payload);

        return redirect()
            ->route('admin_v2.schedule.index')
            ->with('success', 'Schedule saved successfully.');
    }

    /** Try several formats; return [Y-m-d|null, Y-m-d|null] */
    private function normalizeDateRange(?string $from, ?string $to): array
    {
        // Handle "from to to" packed in one field (if your picker returns that)
        if ($from && !$to && str_contains($from, ' to ')) {
            [$from, $to] = explode(' to ', $from);
            $from = trim($from); $to = trim($to);
        }

        $parse = function (?string $v): ?string {
            if (!$v) return null;

            // Accept: 11-10-2025, 11/10/2025, 2025-10-11, etc.
            $formats = ['d-m-Y','d/m/Y','Y-m-d','m/d/Y','m-d-Y'];
            foreach ($formats as $fmt) {
                try { return Carbon::createFromFormat($fmt, $v)->format('Y-m-d'); }
                catch (\Throwable $e) {}
            }
            // last resort
            $ts = strtotime($v);
            return $ts ? date('Y-m-d', $ts) : null;
        };

        return [$parse($from), $parse($to)];
    }

    /**
     * Compute total hours for the range, excluding any days in $restDays.
     * Returns "HH:MM" string.
     */
    private function computeHoursHHMM(?string $from, ?string $to, ?string $start, ?string $end, array $restDays): string
    {
        if (!$from || !$to || !$start || !$end) {
            return '00:00';
        }

        $startT = Carbon::createFromFormat('H:i:s', $start);
        $endT   = Carbon::createFromFormat('H:i:s', $end);

        // Overnight shift support
        $dailyMinutes = (int) $startT->diffInMinutes($endT, false);
        if ($dailyMinutes < 0) {
            $dailyMinutes += 24 * 60;
        }

        $fromD = Carbon::createFromFormat('Y-m-d', $from);
        $toD   = Carbon::createFromFormat('Y-m-d', $to);

        $restUpper = array_map('strtoupper', $restDays);
        $total = 0;

        for ($d = $fromD->copy(); $d->lte($toD); $d->addDay()) {
            // e.g., 'Sunday', 'Monday', ...
            $dayName = strtoupper($d->format('l'));
            if (in_array($dayName, $restUpper, true)) {
                continue; // skip rest day
            }
            $total += $dailyMinutes;
        }

        $hours = intdiv($total, 60);
        $mins  = $total % 60;
        return sprintf('%02d:%02d', max(0,$hours), max(0,$mins));
    }

    
public function showJson(Schedule $schedule)
{
    // return everything the modal needs
    return response()->json([
        'id'            => $schedule->id,
        'reference'     => $schedule->reference,
        'employee'      => $schedule->employee,
        'datefrom'      => $schedule->datefrom,          // "Y-m-d"
        'dateto'        => $schedule->dateto,            // "Y-m-d"
        'start_time'    => $schedule->start_time,        // "HH:MM:SS"
        'end_time'      => $schedule->end_time,
        'max_start_time'=> $schedule->max_start_time,
        'max_end_time'  => $schedule->max_end_time,
        'is_active'     => (int)$schedule->is_active,
        'notes'         => $schedule->notes,
        'restday'       => $schedule->restday,           // comma CSV
        'hours'         => $schedule->hours,
    ]);
}

public function update(Request $request, Schedule $schedule)
{
    $data = $request->validate([
        'employee_reference' => ['required','integer','exists:tbl_people,id'],
        'datefrom'           => ['nullable','string'],
        'dateto'             => ['nullable','string'],
        'start_time'         => ['required','string'],
        'end_time'           => ['required','string'],
        'max_start_time'     => ['required','string'],
        'max_end_time'       => ['required','string'],
        'restday'            => ['nullable','array'],
        'restday.*'          => ['string'],
        'notes'              => ['nullable','string'],
        'is_active'          => ['nullable','boolean'],
    ]);

    // full name
    $person = Person::select('id','firstname','lastname')->findOrFail($data['employee_reference']);
    $employeeFullName = trim($person->lastname . ', ' . $person->firstname);

    // last org mapping
    $org = DB::table('tbl_campus_data')
        ->where('reference', $person->id)
        ->latest('id')
        ->first();
    $campus     = $org->campus     ?? null;
    $department = $org->department ?? null;
    $ministry   = $org->ministry   ?? null;

    // dates -> Y-m-d
    $datefrom = $request->datefrom ? Carbon::parse($request->datefrom)->format('Y-m-d') : null;
    $dateto   = $request->dateto   ? Carbon::parse($request->dateto)->format('Y-m-d')   : null;

    // times -> HH:MM:SS
    $toTime = fn($t) => $t ? date('H:i:s', strtotime($t)) : null;
    $tStart    = $toTime($data['start_time']);
    $tEnd      = $toTime($data['end_time']);
    $tMaxStart = $toTime($data['max_start_time']);
    $tMaxEnd   = $toTime($data['max_end_time']);

    // rest days (CSV) + hours
    $restdays   = $data['restday'] ?? [];
    $restdayCsv = implode(',', $restdays);
    $hoursHHMM  = $this->computeHoursHHMM($datefrom, $dateto, $tStart, $tEnd, $restdays);

    $schedule->update([
        'reference'      => (string)$person->id,
        'employee'       => $employeeFullName,
        'start_time'     => $tStart,
        'end_time'       => $tEnd,
        'max_start_time' => $tMaxStart,
        'max_end_time'   => $tMaxEnd,
        'datefrom'       => $datefrom,
        'dateto'         => $dateto,
        'campus'         => $campus,
        'department'     => $department,
        'ministry'       => $ministry,
        'is_active'      => $request->boolean('is_active') ? 1 : 0,
        'archive'        => 0,
        'notes'          => $data['notes'] ?? null,
        'hours'          => $hoursHHMM ?? '0',
        'restday'        => $restdayCsv,
    ]);

    return redirect()
        ->route('admin_v2.schedule.index')
        ->with('success', 'Schedule updated successfully.');
}

// app/Http/Controllers/AdminV2/ScheduleTimingController.php
public function destroy(\App\Models\Schedule $schedule)
{
    $schedule->delete();

    // If you prefer JSON for ajax, return response()->json(['ok' => true]);
    return redirect()
        ->route('admin_v2.schedule.index')
        ->with('success', 'Schedule deleted successfully.');
}



public function archive(Schedule $schedule)
{
    $schedule->update([
        'archive' => $schedule->archive ? 0 : 1
    ]);
    return response()->json(['success' => true, 'archive' => $schedule->archive]);
}
}
