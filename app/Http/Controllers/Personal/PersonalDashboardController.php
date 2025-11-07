<?php

namespace App\Http\Controllers\Personal;
use DB;
use Carbon\Carbon;
use App\Classes\table;
use App\Classes\permission;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PersonalDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() 
    {        
        $id = \Auth::user()->reference;
        $sm = date('m/01/Y');
        $em = date('m/31/Y');

        $cs = table::schedules()->where([
            ['reference', $id], 
            ['archive', '0']
        ])->first();

        $ps = table::schedules()->where([
            ['reference', $id],
            ['archive', '1'],
        ])->take(8)->get();

        try { $tf = table::settings()->value("time_format"); } catch (\Throwable $e) { $tf = 1; }

        $al = table::leaves()->where([['reference', $id], ['status', 'Approved']])->count();
        $ald = table::leaves()->where([['reference', $id], ['status', 'Approved']])->take(8)->get();
        $pl = table::leaves()->where([['reference', $id], ['status', 'Declined']])->orWhere([['reference', $id], ['status', 'Pending']])->count();
        $a = table::attendance()->where('reference', $id)->latest('date')->take(4)->get();

        $la = table::attendance()->where([['reference', $id], ['status_timein', 'Late Arrival']])->whereBetween('date', [$sm, $em])->count();
        $ed = table::attendance()->where([['reference', $id], ['status_timeout', 'Early Departure']])->whereBetween('date', [$sm, $em])->count();

        // Compute current clock-in status (defensive against mixed date formats)
        $now = Carbon::now();
        $clockedIn = false; $clockedInAt = null;
        try {
            $open = table::attendance()
                ->where('reference', $id)
                ->whereNull('totalhours') // open shift indicator used elsewhere
                ->latest('date')
                ->first();
            if (!$open) {
                $open = table::attendance()
                    ->where('reference', $id)
                    ->whereNull('timeout')
                    ->latest('date')
                    ->first();
            }
            if ($open) {
                $attDate = Carbon::parse($open->date)->toDateString();
                if ($attDate === $now->toDateString()) {
                    $clockedIn = true;
                    $clockedInAt = $open->timein ?? null;
                }
            }
        } catch (\Throwable $e) {
            $clockedIn = false; $clockedInAt = null;
        }

        // Today's schedule summary + next shift countdown
        $scheduleStart = null; $scheduleEnd = null; $nextShiftStartsIn = null;
        try {
            if (!empty($cs) && $cs->intime && $cs->outime) {
                $scheduleStart = $cs->intime;
                $scheduleEnd   = $cs->outime;
                $startToday = Carbon::parse($now->toDateString().' '.$cs->intime);
                if ($now->lt($startToday)) {
                    $nextShiftStartsIn = $now->diffForHumans($startToday, true);
                }
            }
        } catch (\Throwable $e) {}

        // --- KPI metrics: hours today / week / overtime / late this week
        $hoursTodayWorked = 0.0; $hoursTodayExpected = 9.0; // fallback expected
        $hoursWeekWorked  = 0.0; $hoursWeekExpected  = 45.0; // fallback expected
        $overtimeHours    = 0.0; $lateArrivalsWeek   = 0;

        try {
            if (!empty($cs) && isset($cs->hours) && is_numeric($cs->hours)) {
                $hoursTodayExpected = (float) $cs->hours;
                $hoursWeekExpected  = $hoursTodayExpected * 5.0; // basic expectation for the week
            }

            $todayDate = $now->toDateString();
            $rowsToday = table::attendance()->where('reference', $id)
                ->whereDate('date', $todayDate)->get();
            foreach ($rowsToday as $row) {
                if (!empty($row->timein)) {
                    $ti = Carbon::parse($row->date.' '.$row->timein);
                    $to = !empty($row->timeout) ? Carbon::parse($row->date.' '.$row->timeout) : $now;
                    if ($to->lt($ti)) { $to = $now; }
                    $hoursTodayWorked += max(0, $to->floatDiffInMinutes($ti) / 60.0);
                }
            }

            $startOfWeek = (clone $now)->startOfWeek(Carbon::MONDAY)->toDateString();
            $endOfWeek   = (clone $now)->endOfWeek(Carbon::SUNDAY)->toDateString();
            $rowsWeek = table::attendance()->where('reference', $id)
                ->whereBetween('date', [$startOfWeek, $endOfWeek])->get();
            foreach ($rowsWeek as $row) {
                if (!empty($row->timein)) {
                    $ti = Carbon::parse($row->date.' '.$row->timein);
                    $to = !empty($row->timeout) ? Carbon::parse($row->date.' '.$row->timeout) : $ti; // if open, ignore for weekly finalization
                    if ($to->lt($ti)) { continue; }
                    $hoursWeekWorked += max(0, $to->floatDiffInMinutes($ti) / 60.0);
                }
            }

            $lateArrivalsWeek = table::attendance()
                ->where('reference', $id)
                ->where('status_timein', 'Late Arrival')
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->count();

            $overtimeHours = max(0, round($hoursWeekWorked - $hoursWeekExpected, 2));
            $hoursTodayWorked = round($hoursTodayWorked, 2);
            $hoursWeekWorked  = round($hoursWeekWorked, 2);
        } catch (\Throwable $e) {
            // keep defaults
        }
        
        // Guard optional data sources to avoid hard crashes when tables/columns are missing in some environments
        try {
            $total_user_tasks = DB::table('volunteer_tasks')
                ->where('reference', $id)
                ->where('completion_status', 'completed')
                ->sum('task_count');
        } catch (\Throwable $e) {
            $total_user_tasks = 0;
        }

        $today = date('m-d');

        try {
            $birthdaysToday = DB::table('tbl_people')
                ->join('tbl_campus_data', 'tbl_people.id', '=', 'tbl_campus_data.reference')
                ->select('tbl_people.firstname', 'tbl_people.lastname', 'tbl_campus_data.ministry', 'tbl_campus_data.campus')
                ->whereRaw('DATE_FORMAT(birthday, "%m-%d") = DATE_FORMAT(CURDATE(), "%m-%d")')
                ->get();
        } catch (\Throwable $e) {
            $birthdaysToday = collect();
        }

        // Pending leaves (for quick cards)
        try {
            $pendingLeaves = table::leaves()->where('reference', $id)
                ->where('status', 'Pending')->orderByDesc('leavefrom')->take(5)->get();
        } catch (\Throwable $e) {
            $pendingLeaves = collect();
        }

        // Compact profile context
        try {
            $campusContext = table::campusdata2()->where('reference', $id)->first();
        } catch (\Throwable $e) {
            $campusContext = null;
        }

        // Creative tasks assigned to this user
        try {
            $creativeTasks = DB::table('tbl_creative_task_assignments as ta')
                ->join('tbl_creative_tasks as t', 't.id', '=', 'ta.task_id')
                ->join('tbl_creative_requests as r', 'r.id', '=', 't.request_id')
                ->where('ta.people_id', $id)
                ->whereIn('t.status', ['pending', 'in_progress'])
                ->select('t.id', 't.title', 't.description', 't.status', 't.priority', 't.due_at', 'r.request_type', 'r.title as request_title')
                ->orderByRaw("FIELD(t.priority, 'urgent', 'high', 'normal', 'low')")
                ->orderBy('t.due_at')
                ->take(5)
                ->get();
        } catch (\Throwable $e) {
            $creativeTasks = collect();
        }

        return view('personal.personal-dashboard', compact(
            'cs', 'ps', 'al', 'pl', 'ald', 'a', 'la', 'ed', 'tf', 'total_user_tasks','birthdaysToday',
            'clockedIn','clockedInAt','scheduleStart','scheduleEnd','nextShiftStartsIn',
            'hoursTodayWorked','hoursTodayExpected','hoursWeekWorked','hoursWeekExpected','overtimeHours','lateArrivalsWeek',
            'pendingLeaves','campusContext','creativeTasks'
        ));
    }
}
