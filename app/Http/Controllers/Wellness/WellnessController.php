<?php

namespace App\Http\Controllers\Wellness;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class WellnessController extends Controller
{
    // ---- Dashboard: quick check-in + my members + open cases
    public function dashboard(Request $request)
    {
        $userId = Auth::id();

        // (A) Resolve the person id for the logged-in user.
        // Your schema: users.reference == tbl_people.id
        $myPersonId = null;

        if (Schema::hasColumn('users', 'reference')) {
            $myPersonId = DB::table('users')->where('id', $userId)->value('reference');
        }

        // Fallback: match by email (users.email == tbl_people.emailaddress)
        if (!$myPersonId && Schema::hasColumn('users', 'email') && Schema::hasColumn('tbl_people', 'emailaddress')) {
            $email = DB::table('users')->where('id', $userId)->value('email');
            if ($email) {
                $myPersonId = DB::table('tbl_people')->where('emailaddress', $email)->value('id');
            }
        }

        // We prefer person id (tbl_people.id), but also consider users.id as a last-resort
        $possibleLeaderKeys = array_values(array_unique(array_filter([
            $myPersonId, // preferred
            $userId,     // fallback if some rows used users.id historically
        ])));

        // (B) Pull my groups using any matching leader_id
        $myGroups = DB::table('small_groups')
            ->whereIn('leader_id', $possibleLeaderKeys ?: [-1])
            ->pluck('id');

        // Optional: merge co-leader groups from a pivot table if you have one
        if ($myGroups->isEmpty() && Schema::hasTable('small_group_leaders')) {
            $coGroupIds = DB::table('small_group_leaders')
                ->whereIn('leader_id', $possibleLeaderKeys ?: [-1])
                ->pluck('group_id');
            if ($coGroupIds->isNotEmpty()) {
                $myGroups = $myGroups->merge($coGroupIds)->unique();
            }
        }

        // (C) Handle small_group_members column name: person_id vs volunteer_id
        $memberPersonCol = Schema::hasColumn('small_group_members', 'volunteer_id') ? 'volunteer_id' : 'person_id';

        // (D) Build members list
        $members = DB::table('small_group_members as m')
            ->join('tbl_people as p', "p.id", "=", "m.$memberPersonCol")
            ->leftJoin('small_groups as g', 'g.id', '=', 'm.group_id')
            ->select(
                DB::raw("p.id as volunteer_id"),
                'p.firstname', 'p.lastname',
                DB::raw('COALESCE(p.emailaddress,"") as email'),
                DB::raw('COALESCE(p.mobileno,"") as mobileno'),
                'm.group_id',
                'g.name as group_name'
            )
            ->whereIn('m.group_id', $myGroups->isEmpty() ? [0] : $myGroups->toArray())
            ->orderBy('p.firstname')->orderBy('p.lastname')
            ->get();

        // (E) Options for the modal <select>
        $memberOptions = $members->map(function($r){
            return (object) [
                'id'       => (int) $r->volunteer_id,
                'label'    => trim(($r->firstname ?? '') . ' ' . ($r->lastname ?? '')) . ' — ' . ($r->group_name ?? ''),
                'group_id' => (int) $r->group_id,
            ];
        })->values();

        // (F) My open cases
        $myCases = DB::table('wellness_cases as c')
            ->leftJoin('small_groups as g', 'g.id', '=', 'c.group_id')
            ->leftJoin('tbl_people as p', 'p.id', '=', 'c.volunteer_id')
            ->select(
                'c.id',
                DB::raw('c.current_status as status'),
                'c.severity',
                'c.current_stage',
                'c.summary',
                'c.created_at',
                'g.name as group_name',
                DB::raw('TRIM(CONCAT(p.firstname," ",p.lastname)) as volunteer_name')
            )
            ->whereIn('c.group_id', $myGroups->isEmpty() ? [0] : $myGroups->toArray())
            ->whereIn('c.current_status', ['OPEN','UNDER_REVIEW','IN_PROGRESS','WAITING_CONSULT'])
            ->orderByDesc('c.created_at')
            ->limit(50)
            ->get();

        $memberOptionsJson = $memberOptions->toJson(JSON_UNESCAPED_UNICODE);

        return view('wellness.dashboard', compact('members','memberOptions','memberOptionsJson','myCases'));
    }

    // ---- Members for the modal (JSON)
    public function membersJson(Request $request)
    {
        $userId = Auth::id();

        // Resolve person id via users.reference
        $myPersonId = null;
        if (Schema::hasColumn('users','reference')) {
            $myPersonId = DB::table('users')->where('id', $userId)->value('reference');
        }
        if (!$myPersonId && Schema::hasColumn('users','email') && Schema::hasColumn('tbl_people','emailaddress')) {
            $email = DB::table('users')->where('id', $userId)->value('email');
            if ($email) {
                $myPersonId = DB::table('tbl_people')->where('emailaddress', $email)->value('id');
            }
        }

        $possibleLeaderKeys = array_values(array_unique(array_filter([
            $myPersonId,
            $userId,
        ])));

        $myGroups = DB::table('small_groups')
            ->whereIn('leader_id', $possibleLeaderKeys ?: [-1])
            ->pluck('id');

        if ($myGroups->isEmpty() && Schema::hasTable('small_group_leaders')) {
            $coGroupIds = DB::table('small_group_leaders')
                ->whereIn('leader_id', $possibleLeaderKeys ?: [-1])
                ->pluck('group_id');
            if ($coGroupIds->isNotEmpty()) {
                $myGroups = $myGroups->merge($coGroupIds)->unique();
            }
        }

        $memberPersonCol = Schema::hasColumn('small_group_members', 'volunteer_id') ? 'volunteer_id' : 'person_id';

        $rows = DB::table('small_group_members as m')
            ->join('tbl_people as p', "p.id", "=", "m.$memberPersonCol")
            ->leftJoin('small_groups as g', 'g.id', '=', 'm.group_id')
            ->whereIn('m.group_id', $myGroups->isEmpty() ? [0] : $myGroups->toArray())
            ->orderBy('p.firstname')->orderBy('p.lastname')
            ->get([
                DB::raw('p.id as id'),
                'p.firstname','p.lastname',
                'm.group_id',
                DB::raw('COALESCE(g.name,"") as group_name'),
            ]);

        $data = $rows->map(function($r){
            return [
                'id'       => (int) $r->id,
                'label'    => trim(($r->firstname ?? '') . ' ' . ($r->lastname ?? '')) . ($r->group_name ? ' — '.$r->group_name : ''),
                'group_id' => (int) $r->group_id,
            ];
        })->values();

        return response()->json(['ok' => true, 'data' => $data]);
    }

    // ---- Store a wellness check-in with auto-flag rules
    public function storeCheckin(Request $request)
    {
        // 1) Validate
        $data = $request->validate([
            'volunteer_id'      => 'required|integer|exists:tbl_people,id',
            'group_id'          => 'required|integer|exists:small_groups,id',
            'checked_at'        => 'required|date',
            'type'              => 'nullable|string|max:30',
            'emotional'         => 'required|integer|min:1|max:5',
            'physical'          => 'required|integer|min:1|max:5',
            'spiritual'         => 'required|integer|min:1|max:5',
            'attended_service'  => 'sometimes|boolean',
            'notes'             => 'nullable|string',
            'prayer_request'    => 'nullable|string',
            'concerns'          => 'sometimes|array',
            'concerns.*'        => 'nullable|string|max:100',
            'actions'           => 'sometimes|array',
            'actions.*'         => 'nullable|string|max:150',
        ]);

        // Normalize optionals
        $data['attended_service'] = (int) ($data['attended_service'] ?? 0);
        $data['concerns'] = array_values(array_filter($data['concerns'] ?? [], fn($v) => $v !== null && $v !== ''));
        $data['actions']  = array_values(array_filter($data['actions']  ?? [], fn($v) => $v !== null && $v !== ''));

        $now = now();

        // 2) Insert the check-in
        $checkinId = DB::table('wellness_checkins')->insertGetId([
            'group_id'          => $data['group_id'],
            'volunteer_id'      => $data['volunteer_id'],
            'create_by'         => Auth::id(),              // keep your column name
            'checked_at'        => $data['checked_at'],
            'type'              => $data['type'] ?? 'in_person',
            'status'            => 'well',
            'emotional'         => $data['emotional'],
            'physical'          => $data['physical'],
            'spiritual'         => $data['spiritual'],
            'notes'             => $data['notes'] ?? null,
            'concerns_json'     => $data['concerns'] ? json_encode($data['concerns']) : null,
            'attended_service'  => $data['attended_service'],
            'prayer_request'    => $data['prayer_request'] ?? null,
            'actions_json'      => $data['actions'] ? json_encode($data['actions']) : null,
            'created_at'        => $now,
            'updated_at'        => $now,
        ]);

        // 3) Auto-flag rules
        $twoWeeksAgo = now()->subDays(14);

        $hasAttendanceIn2w = DB::table('wellness_checkins')
            ->where('volunteer_id', $data['volunteer_id'])
            ->where('attended_service', 1)
            ->where('checked_at', '>=', $twoWeeksAgo)
            ->exists();

        $lowest = min($data['emotional'], $data['physical'], $data['spiritual']);
        $needsAttention = (!$hasAttendanceIn2w) || ($lowest <= 2);

        if ($needsAttention) {
            // Try to find an existing open case
            $openCase = DB::table('wellness_cases')
                ->where('volunteer_id', $data['volunteer_id'])
                ->whereIn('current_status', ['OPEN','UNDER_REVIEW','IN_PROGRESS','WAITING_CONSULT'])
                ->orderByDesc('created_at')
                ->first();

            if ($openCase) {
                DB::table('wellness_transitions')->insert([
                    'case_id'     => $openCase->id,
                    'from_status' => $openCase->current_status,
                    'to_status'   => 'UNDER_REVIEW',
                    'actor_id'    => Auth::id(),
                    'role'        => 'SMALLGROUP_LEADER',
                    'note'        => 'Auto-flag: recent check-in triggered review.',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);

                DB::table('wellness_cases')->where('id', $openCase->id)->update([
                    'current_status'   => 'UNDER_REVIEW',
                    'current_stage'    => 'ml',
                    'severity'         => $lowest <= 2 ? 'high' : 'medium',
                    'summary'          => 'Auto-flagged by check-in rules.',
                    'last_activity_at' => $now,
                    'updated_at'       => $now,
                ]);
            } else {
                $caseId = DB::table('wellness_cases')->insertGetId([
                    'group_id'               => $data['group_id'],
                    'volunteer_id'           => $data['volunteer_id'],
                    'opened_from_checkin_id' => $checkinId,
                    'opened_by'              => Auth::id(),
                    'assigned_to_id'         => null,
                    'ministry_leader_id'     => null,
                    'overseer_id'            => null,
                    'current_status'         => 'UNDER_REVIEW',
                    'current_stage'          => 'ml',
                    'severity'               => $lowest <= 2 ? 'high' : 'medium',
                    'summary'                => 'Auto-flagged by check-in rules.',
                    'last_activity_at'       => $now,
                    'created_at'             => $now,
                    'updated_at'             => $now,
                ]);

                DB::table('wellness_transitions')->insert([
                    'case_id'     => $caseId,
                    'from_status' => 'OPEN',
                    'to_status'   => 'UNDER_REVIEW',
                    'actor_id'    => Auth::id(),
                    'role'        => 'SMALLGROUP_LEADER',
                    'note'        => 'Auto-created from wellness check-in.',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        return back()->with('success', $needsAttention
            ? 'Check-in saved and case flagged for review.'
            : 'Check-in saved.');
    }

    // ---- Cases (list)
    public function casesIndex(Request $request)
    {
        $q = DB::table('wellness_cases as c')
            ->leftJoin('tbl_people as p','p.id','=','c.volunteer_id')
            ->leftJoin('small_groups as g','g.id','=','c.group_id')
            ->select(
                'c.id',
                DB::raw('c.current_status as status'),
                'c.severity',
                'c.current_stage',
                'c.summary',
                'c.created_at',
                'g.name as group_name',
                DB::raw('TRIM(CONCAT(p.firstname," ",p.lastname)) as volunteer_name')
            )
            ->orderByDesc('c.created_at');

        if ($term = trim($request->query('q',''))) {
            $q->where(function($w) use ($term){
                $w->where('c.summary','like',"%$term%")
                  ->orWhere('g.name','like',"%$term%")
                  ->orWhere(DB::raw('TRIM(CONCAT(p.firstname," ",p.lastname))'),'like',"%$term%");
            });
        }

        $cases = $q->paginate(20)->appends($request->query());

        return view('wellness.cases_index', compact('cases'));
    }

    // ---- Case detail (timeline)
    public function caseShow(int $id)
    {
        $case = DB::table('wellness_cases as c')
            ->leftJoin('tbl_people as p','p.id','=','c.volunteer_id')
            ->leftJoin('small_groups as g','g.id','=','c.group_id')
            ->select(
                'c.*',
                DB::raw('TRIM(CONCAT(p.firstname," ",p.lastname)) as volunteer_name'),
                'g.name as group_name'
            )->where('c.id',$id)->first();

        abort_if(!$case, 404);

        $transitions = DB::table('wellness_transitions as t')
            ->leftJoin('users as u','u.id','=','t.actor_id')
            ->select('t.*', DB::raw('COALESCE(u.name, CONCAT("User#",t.actor_id)) as actor_name'))
            ->where('t.case_id', $id)
            ->orderBy('t.created_at')
            ->get();

        // last few checkins
        $checkins = DB::table('wellness_checkins')
            ->where('volunteer_id', $case->volunteer_id)
            ->orderByDesc('checked_at')
            ->limit(5)
            ->get();

        return view('wellness.case_show', compact('case','transitions','checkins'));
    }

    // ---- Quick assignment
    public function assign(Request $request, int $id)
    {
        $request->validate([
            'assigned_to_id' => 'nullable|integer|exists:users,id'
        ]);

        DB::table('wellness_cases')->where('id',$id)->update([
            'assigned_to_id' => $request->assigned_to_id,
            'updated_at'     => now(),
        ]);

        DB::table('wellness_transitions')->insert([
            'case_id'     => $id,
            'from_status' => 'assigned',
            'to_status'   => 'assigned',
            'actor_id'    => Auth::id(),
            'role'        => 'ML/WTL/MO',
            'note'        => 'Assignment changed.',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('success','Case assignment updated.');
    }

    // ---- Generic transition add (comment + status update)
    public function transition(Request $request, int $id)
    {
        $data = $request->validate([
            'from_status' => 'required|string|max:50',
            'to_status'   => 'required|string|max:50',
            'note'        => 'nullable|string',
        ]);

        $to = strtoupper($data['to_status']);

        DB::table('wellness_transitions')->insert([
            'case_id'     => $id,
            'from_status' => strtoupper($data['from_status']),
            'to_status'   => $to,
            'actor_id'    => Auth::id(),
            'role'        => 'COMMENT',
            'note'        => $data['note'] ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        DB::table('wellness_cases')->where('id',$id)->update([
            'current_status'   => $to,
            'last_activity_at' => now(),
            'updated_at'       => now(),
        ]);

        return back()->with('success','Case updated.');
    }

    // ---- Propose close (ML/WTL)
    public function proposeClose(Request $request, int $id)
    {
        DB::table('wellness_transitions')->insert([
            'case_id'     => $id,
            'from_status' => 'in_progress',
            'to_status'   => 'close_proposed',
            'actor_id'    => Auth::id(),
            'role'        => 'PROPOSE_CLOSE',
            'note'        => $request->input('note'),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        DB::table('wellness_cases')->where('id',$id)->update([
            'current_status'   => 'close_proposed',
            'last_activity_at' => now(),
            'updated_at'       => now(),
        ]);

        return back()->with('success','Close case proposed. Awaiting overseer approval.');
    }

    // ---- Approve close (Overseer)
    public function approveClose(Request $request, int $id)
    {
        DB::table('wellness_transitions')->insert([
            'case_id'     => $id,
            'from_status' => 'close_proposed',
            'to_status'   => 'closed',
            'actor_id'    => Auth::id(),
            'role'        => 'OVERSEER',
            'note'        => $request->input('note'),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        DB::table('wellness_cases')->where('id',$id)->update([
            'current_status' => 'closed',
            'closed_at'      => now(),
            'closure_notes'  => $request->input('note'),
            'updated_at'     => now(),
        ]);

        return redirect()->route('wellness.cases.index')->with('success','Case closed.');
    }
}