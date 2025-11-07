<?php

namespace App\Http\Controllers\RadioDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    // ===================== LIST + FILTERS =====================
    public function tasksIndex(Request $request)
    {
        // Filters
        $siteId = $request->input('site_id');           // radio_stations.id
        $type   = $request->input('type');              // Preventive | Corrective
        $state  = $request->input('state');             // open|scheduled|overdue|done (computed)
        $q      = trim((string) $request->input('q'));  // search

        // Picklists
        $sites      = DB::table('radio_stations')->orderBy('name')->get(['id','name']);
        $assignees  = DB::table('users')->orderBy('name')->get(['id','name']);

        // Base query
        $tasksQ = DB::table('radio_maintenance as m')
            ->leftJoin('radio_stations as rs', 'rs.id', '=', 'm.station_id')
            ->leftJoin('users as u', 'u.id', '=', 'm.assignee_id')
            ->select([
                'm.id','m.title','m.type','m.priority','m.status','m.due',
                'm.notes','m.created_at','m.updated_at',
                'm.station_id',
                DB::raw("COALESCE(rs.name,'—') as site"),
                DB::raw("COALESCE(u.name,'—') as assignee"),
            ]);

        // Apply filters
        if ($siteId) {
            $tasksQ->where('m.station_id', $siteId);
        }
        if ($type) {
            $tasksQ->where('m.type', $type);
        }
        if ($q !== '') {
            $tasksQ->where(function($qq) use ($q) {
                $qq->where('m.title', 'like', "%{$q}%")
                   ->orWhere('m.notes', 'like', "%{$q}%")
                   ->orWhere('rs.name', 'like', "%{$q}%");
            });
        }

        // Computed state filter
        $today = Carbon::today()->toDateString();
        switch ($state) {
            case 'open':
                $tasksQ->where('m.status', 'Open');
                break;
            case 'scheduled':
                $tasksQ->where('m.status', 'Scheduled');
                break;
            case 'overdue':
                $tasksQ->where(function($qq) use ($today) {
                    $qq->where('m.status', '<>', 'Closed')
                       ->where('m.due', '<', $today);
                });
                break;
            case 'done':
                $tasksQ->where('m.status', 'Closed');
                break;
        }

        // Order: overdue → scheduled → open → closed, then due asc
        $tasks = $tasksQ->orderByRaw("
                    CASE m.status
                        WHEN 'Overdue'   THEN 0
                        WHEN 'Scheduled' THEN 1
                        WHEN 'Open'      THEN 2
                        ELSE 3
                    END
                ")
                ->orderBy('m.due')
                ->get();

        // Summary stats (global – not constrained by current filters)
        $statsBase = DB::table('radio_maintenance');
        $stats = [
            'open'         => (clone $statsBase)->where('status','Open')->count(),
            'overdue'      => (clone $statsBase)->where('status','<>','Closed')->where('due','<',$today)->count(),
            'scheduled'    => (clone $statsBase)->where('status','Scheduled')->count(),
            'doneThisWeek' => (clone $statsBase)->where('status','Closed')
                                  ->whereBetween('updated_at',[Carbon::now()->subDays(7), Carbon::now()])
                                  ->count(),
        ];

        return view('radio_dashboard.maintenance.tasks.index', compact('stats','tasks','sites','assignees'));
    }

    // ===================== CREATE (modal) =====================
   public function storeTask(Request $request)
{
    $data = $request->validate([
        'title'       => ['required','string','max:200'],
        'station_id'  => ['required','integer','exists:radio_stations,id'],
        'type'        => ['required','in:Preventive,Corrective'],
        'priority'    => ['required','in:Low,Medium,High,Critical'],
        'due'         => ['required','date'],
        'status'      => ['required','in:Open,Scheduled,Overdue,Closed'],
        'assignee_id' => ['nullable','integer','exists:users,id'],
        'notes'       => ['nullable','string','max:2000'],
    ]);

    // Resolve denormalized fields your table expects
    $stationName  = DB::table('radio_stations')->where('id', $data['station_id'])->value('name');
    $assigneeName = null;
    if (!empty($data['assignee_id'])) {
        $assigneeName = DB::table('users')->where('id', $data['assignee_id'])->value('name');
    }

    // Absolutely guarantee 'site' is non-null/non-empty for NOT NULL column
    $siteForRow = $stationName ?: ('Unknown Station #'.$data['station_id']);

    // Normalize due to YYYY-MM-DD
    $dueDate = Carbon::parse($data['due'])->toDateString();

    try {
        DB::table('radio_maintenance')->insert([
            'title'        => $data['title'],
            'station_id'   => $data['station_id'],
            'site'         => $siteForRow,                 // REQUIRED in your schema
            'type'         => $data['type'],
            'priority'     => $data['priority'],
            'due'          => $dueDate,
            'status'       => $data['status'],
            'assignee_id'  => $data['assignee_id'] ?? null,
            'assignee'     => $assigneeName,               // nullable in schema
            'notes'        => $data['notes'] ?? null,
            'created_by'   => Auth::id(),                  // nullable in schema
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    } catch (\Throwable $e) {
        // Log *and* (in local) show the exact error so you can fix quickly
        Log::error('maintenance store failed', [
            'sql_error' => $e->getMessage(),
            'payload'   => $data,
        ]);

        if (config('app.debug')) {
            return back()->withInput()->withErrors([
                'store' => 'Could not create task: '.$e->getMessage(),
            ]);
        }

        return back()->withInput()->withErrors(['store'=>'Could not create task.']);
    }

    return back()->with('success','Task created.');
}

    // ===================== MARK DONE =====================
    public function markDone(Request $request, int $id)
    {
        try {
            DB::table('radio_maintenance')
                ->where('id', $id)
                ->update([
                    'status'       => 'Closed',
                    'completed_at' => now(),
                    'updated_at'   => now(),
                ]);
        } catch (\Throwable $e) {
            Log::error('maintenance done failed', ['err'=>$e->getMessage(), 'id'=>$id]);
            return back()->withErrors(['done'=>'Could not mark as done.']);
        }

        return back()->with('success', 'Task closed.');
    }

    // ===================== RESCHEDULE =====================
    public function reschedule(Request $request, int $id)
    {
        $data = $request->validate([
            'due' => ['required','date'],
        ]);

        try {
            DB::table('radio_maintenance')
                ->where('id', $id)
                ->update([
                    'due'        => $data['due'],
                    'status'     => 'Scheduled',
                    'updated_at' => now(),
                ]);
        } catch (\Throwable $e) {
            Log::error('maintenance reschedule failed', ['err'=>$e->getMessage(), 'id'=>$id, 'payload'=>$data]);
            return back()->withErrors(['reschedule'=>'Could not reschedule task.']);
        }

        return back()->with('success', 'Task rescheduled.');
    }

    // ===================== OPTIONAL CALENDAR =====================
    public function calendar()
    {
        // Simple skeleton so the link works; build out as needed
        $today = Carbon::now();
        return view('radio_dashboard.maintenance.calendar.index', compact('today'));
    }
}