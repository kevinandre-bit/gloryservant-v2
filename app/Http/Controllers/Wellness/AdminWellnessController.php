<?php

namespace App\Http\Controllers\Wellness;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminWellnessController extends Controller
{
    public function index(Request $request)
    {
        // --------- Filters ----------
        $status   = trim($request->query('status', ''));     // OPEN / UNDER_REVIEW / IN_PROGRESS / WAITING_CONSULT / CLOSED / …
        $stage    = trim($request->query('stage', ''));      // ml / wtl / mo
        $severity = trim($request->query('severity', ''));   // low / medium / high
        $q        = trim($request->query('q', ''));          // free text
        $from     = trim($request->query('from',''));        // YYYY-MM-DD
        $to       = trim($request->query('to',''));          // YYYY-MM-DD

        // SLA days (ETA = created_at + SLA, unless we have a nearer followup_due_on)
        $slaDays = [
            'high'   => 2,
            'medium' => 5,
            'low'    => 7,
        ];

        // --------- Base query (cases list) ----------
        $casesQ = DB::table('wellness_cases as c')
            ->leftJoin('small_groups as g', 'g.id', '=', 'c.group_id')
            ->leftJoin('tbl_people as p', 'p.id', '=', 'c.volunteer_id')
            ->leftJoin('users as au', 'au.id', '=', 'c.assigned_to_id')
            ->select(
                'c.id',
                'c.group_id',
                'c.volunteer_id',
                'c.current_status',
                'c.current_stage',
                'c.severity',
                'c.title',
                'c.summary',
                'c.created_at',
                'c.last_activity_at',
                'c.closed_at',
                'c.assigned_to_id',
                DB::raw('TRIM(CONCAT(p.firstname," ",p.lastname)) as volunteer_name'),
                'g.name as group_name',
                'au.name as assigned_to_name'
            );

        if ($status !== '')   $casesQ->where('c.current_status', $status);
        if ($stage !== '')    $casesQ->where('c.current_stage', $stage);
        if ($severity !== '') $casesQ->where('c.severity', $severity);
        if ($from !== '')     $casesQ->where('c.created_at', '>=', $from.' 00:00:00');
        if ($to !== '')       $casesQ->where('c.created_at', '<=', $to.' 23:59:59');

        if ($q !== '') {
            $casesQ->where(function($w) use ($q) {
                $w->where('c.summary', 'like', "%$q%")
                  ->orWhere('c.title', 'like', "%$q%")
                  ->orWhere('g.name', 'like', "%$q%")
                  ->orWhere(DB::raw('TRIM(CONCAT(p.firstname," ",p.lastname))'), 'like', "%$q%");
            });
        }

        $casesQ->orderByRaw("
            CASE c.current_status
                WHEN 'UNDER_REVIEW'   THEN 1
                WHEN 'IN_PROGRESS'    THEN 2
                WHEN 'WAITING_CONSULT'THEN 3
                WHEN 'OPEN'           THEN 4
                ELSE 5
            END
        ")->orderByDesc('c.created_at');

        $cases = $casesQ->paginate(20)->appends($request->query());

        // --------- Compute ETA & Overdue (per page items) ----------
        // If there are followup dates stored in wellness_checkins.followup_due_on, pick the nearest future one.
        $caseIds = $cases->pluck('id')->all();

        $nearestFollowups = [];
        if (!empty($caseIds)) {
            // Find the nearest followup due date per volunteer/group from recent checkins
            $followups = DB::table('wellness_checkins as wc')
                ->join('wellness_cases as c', function($j){
                    $j->on('c.volunteer_id','=','wc.volunteer_id')
                      ->on('c.group_id','=','wc.group_id');
                })
                ->whereIn('c.id', $caseIds)
                ->whereNotNull('wc.followup_due_on')
                ->select('c.id as case_id', DB::raw('MIN(wc.followup_due_on) as next_due'))
                ->groupBy('c.id')
                ->get();

            foreach ($followups as $f) {
                $nearestFollowups[(int)$f->case_id] = $f->next_due;
            }
        }

        $now = now();
        $rows = $cases->getCollection()->map(function($c) use ($slaDays, $nearestFollowups, $now) {
            $sla = $slaDays[$c->severity] ?? 7;
            $eta = $nearestFollowups[$c->id] ?? $now->copy()->parse($c->created_at)->addDays($sla)->toDateString();
            $overdue = $eta < $now->toDateString() && !in_array($c->current_status, ['CLOSED','RESOLVED','ARCHIVED']);

            $c->eta_date = $eta;
            $c->is_overdue = $overdue;
            return $c;
        });
        $cases->setCollection($rows);

        // --------- KPIs ----------
        $kpis = DB::table('wellness_cases')
            ->selectRaw("
                SUM(CASE WHEN current_status IN ('OPEN','UNDER_REVIEW','IN_PROGRESS','WAITING_CONSULT') THEN 1 ELSE 0 END) as open_total,
                SUM(CASE WHEN severity='high' THEN 1 ELSE 0 END) as high_total,
                SUM(CASE WHEN severity='medium' THEN 1 ELSE 0 END) as med_total,
                SUM(CASE WHEN severity='low' THEN 1 ELSE 0 END) as low_total,
                SUM(CASE WHEN current_status='CLOSED' THEN 1 ELSE 0 END) as closed_total
            ")
            ->first();

        $statusBreakdown = DB::table('wellness_cases')
            ->select('current_status', DB::raw('COUNT(*) as c'))
            ->groupBy('current_status')
            ->orderBy('current_status')
            ->get();

        // quick list of assignable users (adjust to your org)
        $assignable = DB::table('users')->select('id','name')->orderBy('name')->get();

        return view('wellness.admin_dashboard', compact(
            'cases', 'kpis', 'statusBreakdown', 'assignable',
            'status','stage','severity','q','from','to'
        ));
    }

    public function assign(Request $request, int $id)
    {
        $request->validate([
            'assigned_to_id' => 'nullable|integer|exists:users,id'
        ]);

        DB::table('wellness_cases')->where('id',$id)->update([
            'assigned_to_id' => $request->assigned_to_id,
            'updated_at'     => now(),
            'last_activity_at'=> now(),
        ]);

        DB::table('wellness_transitions')->insert([
            'case_id'     => $id,
            'from_status' => 'ASSIGN',
            'to_status'   => 'ASSIGN',
            'actor_id'    => Auth::id(),
            'role'        => 'ADMIN',
            'note'        => 'Admin updated assignment.',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('success','Assignment saved.');
    }
}