<?php

namespace App\Http\Controllers\Wellness;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FollowupController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $from = $request->query('from', '');
        $to   = $request->query('to', '');
        $q    = trim($request->query('q', ''));

        // Volunteers dropdown
        $volunteerOptions = DB::table('tbl_people')
            ->select('id','firstname','lastname')
            ->orderBy('firstname')->orderBy('lastname')
            ->get()
            ->map(fn($p)=>[
                'id'    => (int)$p->id,
                'label' => trim(($p->firstname ?? '').' '.($p->lastname ?? '')),
            ])->values();

        // Leader KPIs
        $kpis = (object) [
            'total'         => DB::table('volunteer_followups')->where('leader_id', $userId)->count(),
            'last7'         => DB::table('volunteer_followups')->where('leader_id', $userId)->where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            'this_month'    => DB::table('volunteer_followups')->where('leader_id', $userId)
                                   ->whereBetween('meeting_date', [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()])
                                   ->count(),
            'unique_people' => DB::table('volunteer_followups')->where('leader_id', $userId)->distinct('volunteer_id')->count('volunteer_id'),
        ];

        // Base query (scoped to leader)
        $base = DB::table('volunteer_followups as f')
            ->leftJoin('tbl_people as p','p.id','=','f.volunteer_id')
            ->leftJoin('small_groups as g','g.id','=','f.group_id')
            ->select(
                'f.*',
                DB::raw('TRIM(CONCAT(p.firstname," ",p.lastname)) as volunteer_name'),
                'g.name as group_name'
            )
            ->where('f.leader_id', $userId);

        // Apply filters
        if ($from !== '') $base->whereDate('f.meeting_date', '>=', $from);
        if ($to   !== '') $base->whereDate('f.meeting_date', '<=', $to);
        if ($q    !== '') {
            $base->where(function($w) use ($q){
                $w->where('f.notes','like',"%$q%")
                  ->orWhere('g.name','like',"%$q%")
                  ->orWhere(DB::raw('TRIM(CONCAT(p.firstname," ",p.lastname))'),'like',"%$q%");
            });
        }

        $followups = $base->orderByDesc('f.meeting_date')->paginate(20)->appends($request->query());

        // Timeline (last 7 by me)
        $timeline = DB::table('volunteer_followups as f')
            ->leftJoin('tbl_people as p','p.id','=','f.volunteer_id')
            ->leftJoin('users as u','u.id','=','f.leader_id')
            ->select(
                'f.id','f.created_at','f.conversation_json',
                DB::raw('TRIM(CONCAT(p.firstname," ",p.lastname)) as volunteer_name'),
                'u.name as leader_name'
            )
            ->where('f.leader_id',$userId)
            ->orderByDesc('f.created_at')->limit(7)->get();

        return view('wellness.followups.index', compact('volunteerOptions','followups','timeline','from','to','q','kpis'));
    }

    public function store(Request $request)
    {
        // require next steps + due date (Phase 1 rule)
        $data = $request->validate([
            'volunteer_id'     => 'required|integer|exists:tbl_people,id',
            'meeting_date'     => 'required|date',
            'reasons'          => 'sometimes|array',
            'reasons.*'        => 'string|max:200',
            'reason_other'     => 'nullable|string|max:200',
            'conversation'     => 'required|string|max:200',
            'response'         => 'required|string|max:200',
            'next_steps'       => 'required|array|min:1',
            'next_steps.*'     => 'string|max:200',
            'next_step_other'  => 'nullable|string|max:200',
            'notes'            => 'nullable|string',
            'severity'         => 'nullable|in:low,medium,high',
            'status'           => 'nullable|in:open,in_progress,resolved',
            'followup_due_on'  => 'required|date',
            'assigned_to_id'   => 'nullable|integer|exists:users,id',
            'checkin_id'       => 'nullable|integer|exists:wellness_checkins,id',
            'escalate_to_ml'   => 'nullable|boolean',
            'attachment'       => 'nullable|file|max:5120|mimes:pdf,png,jpg,jpeg,doc,docx',
        ]);

        // group auto-detect
        $groupId = DB::table('small_group_members')
            ->where('volunteer_id', $data['volunteer_id'])
            ->value('group_id');

        // reasons normalize
        $reasons = $request->input('reasons', []);
        if (($request->input('reason') === 'other') || in_array('other', $reasons ?? [])) {
            if ($request->filled('reason_other')) $reasons[] = $request->reason_other;
            $reasons = array_values(array_filter($reasons));
        }

        // steps normalize
        $nextSteps = $request->input('next_steps', []);
        if (($request->input('next_step') === 'other') || in_array('other', $nextSteps ?? [])) {
            if ($request->filled('next_step_other')) $nextSteps[] = $request->next_step_other;
            $nextSteps = array_values(array_filter($nextSteps));
        }

        $conversationArr = [$request->conversation];
        $responseArr     = [$request->response];

        // PRIVATE attachment storage
        $attPath = $attName = null;
        $attSize = null;
        if ($request->hasFile('attachment')) {
            // disk 'private' must exist in filesystems.php
            $stored = $request->file('attachment')->store('followups', 'private');
            $attPath = $stored;
            $attName = $request->file('attachment')->getClientOriginalName();
            $attSize = $request->file('attachment')->getSize();
        }

        DB::table('volunteer_followups')->insert([
            'leader_id'        => Auth::id(),
            'volunteer_id'     => $data['volunteer_id'],
            'group_id'         => $groupId,
            'meeting_date'     => $data['meeting_date'],
            'categories_json'  => json_encode($reasons, JSON_UNESCAPED_UNICODE),
            'reasons_json'     => json_encode($reasons, JSON_UNESCAPED_UNICODE),
            'conversation_json'=> json_encode($conversationArr, JSON_UNESCAPED_UNICODE),
            'response_json'    => json_encode($responseArr, JSON_UNESCAPED_UNICODE),
            'next_steps_json'  => json_encode($nextSteps, JSON_UNESCAPED_UNICODE),
            'notes'            => $request->input('notes'),
            'severity'         => $data['severity'] ?? 'medium',
            'status'           => 'open',
            'followup_due_on'  => $data['followup_due_on'],
            'assigned_to_id'   => $data['assigned_to_id'] ?? Auth::id(),
            'checkin_id'       => $data['checkin_id'] ?? null,
            'escalate_to_ml'   => (int)($data['escalate_to_ml'] ?? 0),
            'attachment_path'  => $attPath,
            'attachment_name'  => $attName,
            'attachment_size'  => $attSize,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return back()->with('success','Follow-up recorded successfully.');
    }

    // JSON for view modal (with auth)
    public function show(int $id)
    {
        abort_unless(Gate::allows('view-followup', $id), 403);

        $has = fn($table,$col)=> \Schema::hasTable($table) && \Schema::hasColumn($table,$col);

        $q = DB::table('volunteer_followups as f')
            ->leftJoin('tbl_people as p','p.id','=','f.volunteer_id')
            ->leftJoin('tbl_campus_data as c','c.id','=','p.id')
            ->leftJoin('small_groups as g','g.id','=','f.group_id')
            ->leftJoin('users as u','u.id','=','f.leader_id')
            ->select(
                'f.*',
                DB::raw('TRIM(CONCAT(p.firstname," ",p.lastname)) as volunteer_name'),
                'u.name as leader_name',
                'g.name as group_name',
                DB::raw('COALESCE(p.mobileno,"") as phone'),
                DB::raw('COALESCE(p.emailaddress,"") as email'),
            );

        if ($has('tbl_campus_data','campus'))   $q->addSelect(DB::raw('COALESCE(c.campus,"") as campus')); else $q->addSelect(DB::raw('"" as campus'));
        if ($has('tbl_campus_data','ministry')) $q->addSelect(DB::raw('COALESCE(c.ministry,"") as ministry')); else $q->addSelect(DB::raw('"" as ministry'));
        if ($has('tbl_campus_data','department')) $q->addSelect(DB::raw('COALESCE(c.department,"") as department')); else $q->addSelect(DB::raw('"" as department'));

        $row = $q->where('f.id',$id)->first();
        abort_if(!$row,404);

        $row->reasons      = json_decode($row->reasons_json ?: '[]', true);
        $row->conversation = json_decode($row->conversation_json ?: '[]', true);
        $row->response     = json_decode($row->response_json ?: '[]', true);
        $row->next_steps   = json_decode($row->next_steps_json ?: '[]', true);

        return response()->json($row);
    }

    // Minimal helpers for Phase 1

    public function updateDueDate(Request $request, int $id)
    {
        abort_unless(Gate::allows('update-followup', $id), 403);

        $data = $request->validate(['followup_due_on' => 'required|date']);
        DB::table('volunteer_followups')->where('id',$id)->update([
            'followup_due_on' => $data['followup_due_on'],
            'updated_at' => now(),
        ]);
        return back()->with('success','Due date updated.');
    }

    public function selfAssign(int $id)
    {
        // owners and admins can call this; if not owner, allow but require admin? For Phase 1: allow owner
        abort_unless(Gate::allows('update-followup', $id), 403);

        DB::table('volunteer_followups')->where('id',$id)->update([
            'assigned_to_id' => Auth::id(),
            'updated_at' => now(),
        ]);
        return back()->with('success','Assigned to you.');
    }
}