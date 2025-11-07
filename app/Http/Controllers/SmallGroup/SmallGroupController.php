<?php

namespace App\Http\Controllers\SmallGroup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SmallGroupController extends Controller
{
    public function index(Request $request)
    {
        // ---- Groups list with leader name + member count (STRICT-safe grouping)
        $groups = DB::table('small_groups as g')
            ->leftJoin('small_group_members as m', 'm.group_id', '=', 'g.id')
            ->leftJoin('tbl_people as lp', 'lp.id', '=', 'g.leader_id')
            ->selectRaw('
                g.id,
                g.name,
                g.description,
                g.leader_id,
                COALESCE(CONCAT(lp.firstname, " ", lp.lastname), "") as leader_name,
                COUNT(m.id) as members_count
            ')
            ->groupBy('g.id', 'g.name', 'g.description', 'g.leader_id', 'lp.firstname', 'lp.lastname')
            ->orderBy('g.name')
            ->get();

        // ---- Detect optional structures
        $hasCampusId     = Schema::hasColumn('tbl_people', 'campus_id');
        $hasMinistryId   = Schema::hasColumn('tbl_people', 'ministry_id');
        $hasCampusTable  = Schema::hasTable('campuses');
        $hasMinistryTbl  = Schema::hasTable('ministries');
        $hasEmailColumn  = Schema::hasColumn('tbl_people', 'email');     // optional
        $hasMobileno     = Schema::hasColumn('tbl_people', 'mobileno');  // expected

        // ---- Base people query (volunteers source)
        $pq = DB::table('tbl_people as p')->select('p.id', 'p.firstname', 'p.lastname');

        // Try to expose campus/ministry names if possible
        if ($hasCampusId && $hasCampusTable && Schema::hasColumn('campuses', 'name')) {
            $pq->leftJoin('campuses as c', 'c.id', '=', 'p.campus_id')
               ->addSelect(DB::raw('COALESCE(c.name, "") as campus'));
        } else {
            // fallback: literal empty string
            $pq->addSelect(DB::raw('"" as campus'));
        }

        if ($hasMinistryId && $hasMinistryTbl && Schema::hasColumn('ministries', 'name')) {
            $pq->leftJoin('ministries as mi', 'mi.id', '=', 'p.ministry_id')
               ->addSelect(DB::raw('COALESCE(mi.name, "") as ministry'));
        } else {
            $pq->addSelect(DB::raw('"" as ministry'));
        }

        // Phone/email (optional)
        if ($hasMobileno) {
            $pq->addSelect('p.mobileno');
        } else {
            $pq->addSelect(DB::raw('"" as mobileno'));
        }
        if ($hasEmailColumn) {
            $pq->addSelect('p.email');
        } else {
            $pq->addSelect(DB::raw('"" as email'));
        }

        $people = $pq->orderBy('p.firstname')->orderBy('p.lastname')->get();

        // Map of volunteer_id -> { group_id, group_name }
        $memberships = DB::table('small_group_members as sm')
            ->join('small_groups as g', 'g.id', '=', 'sm.group_id')
            ->select('sm.volunteer_id', 'sm.group_id', 'g.name as group_name')
            ->get()
            ->keyBy('volunteer_id');

        // ---- Active group details
        $activeId    = $request->query('group_id');
        $activeGroup = null;
        $members     = collect();

        if ($activeId) {
            $activeGroup = DB::table('small_groups')->where('id', $activeId)->first();

            $mq = DB::table('small_group_members as m')
                ->join('tbl_people as p', 'p.id', '=', 'm.volunteer_id')
                ->where('m.group_id', $activeId)
                ->select('p.id as volunteer_id', 'p.firstname', 'p.lastname', 'm.is_leader');

            if ($hasMobileno) $mq->addSelect('p.mobileno'); else $mq->addSelect(DB::raw('"" as mobileno'));
            if ($hasEmailColumn) $mq->addSelect('p.email'); else $mq->addSelect(DB::raw('"" as email'));

            if ($hasCampusId && $hasCampusTable && Schema::hasColumn('campuses', 'name')) {
                $mq->leftJoin('campuses as c', 'c.id', '=', 'p.campus_id')
                   ->addSelect(DB::raw('COALESCE(c.name, "") as campus'));
            } else {
                $mq->addSelect(DB::raw('"" as campus'));
            }
            if ($hasMinistryId && $hasMinistryTbl && Schema::hasColumn('ministries', 'name')) {
                $mq->leftJoin('ministries as mi', 'mi.id', '=', 'p.ministry_id')
                   ->addSelect(DB::raw('COALESCE(mi.name, "") as ministry'));
            } else {
                $mq->addSelect(DB::raw('"" as ministry'));
            }

            $members = $mq->orderBy('p.firstname')->orderBy('p.lastname')->get();
        }

        // ---- JSON list for frontend
        $peopleArray = $people->map(function ($p) use ($memberships) {
            $mem = $memberships->get($p->id);
            return [
                'id'         => (int) $p->id,
                'name'       => trim(($p->firstname ?? '') . ' ' . ($p->lastname ?? '')),
                'phone'      => $p->mobileno ?? '',
                'email'      => $p->email ?? '',
                'campus'     => $p->campus ?? '',
                'ministry'   => $p->ministry ?? '',
                'group_id'   => $mem->group_id   ?? null,
                'group_name' => $mem->group_name ?? '',
            ];
        })->values();

        // ---- Preselected IDs for the current group (so checkboxes appear ticked)
        $memberIds = $members->pluck('volunteer_id')->map(function ($v) { return (int) $v; })->all();

        return view('admin.small_group.index', compact(
            'groups', 'people', 'activeGroup', 'members', 'activeId',
            'peopleArray', 'memberIds'
        ));
    }

    public function storeGroup(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'leader_id'   => 'nullable|integer|exists:tbl_people,id',
        ]);

        DB::beginTransaction();
        try {
            $groupId = DB::table('small_groups')->insertGetId([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'leader_id'   => $data['leader_id'] ?? null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            if (!empty($data['leader_id'])) {
                DB::table('small_group_members')->updateOrInsert(
                    ['group_id' => $groupId, 'volunteer_id' => $data['leader_id']],
                    ['is_leader' => 1, 'updated_at' => now()]
                );
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Create small group failed', ['err' => $e->getMessage()]);
            return back()->withErrors(['group' => 'Could not create small group.']);
        }

        return redirect()->route('small.index', ['group_id' => $groupId])
            ->with('success', 'Small group created.');
    }

    public function updateLeader(Request $request, int $group)
    {
        $data = $request->validate([
            'leader_id' => 'nullable|integer|exists:tbl_people,id',
        ]);

        DB::beginTransaction();
        try {
            DB::table('small_groups')->where('id', $group)->update([
                'leader_id'  => $data['leader_id'] ?? null,
                'updated_at' => now(),
            ]);

            DB::table('small_group_members')
                ->where('group_id', $group)
                ->update(['is_leader' => 0, 'updated_at' => now()]);

            if (!empty($data['leader_id'])) {
                DB::table('small_group_members')->updateOrInsert(
                    ['group_id' => $group, 'volunteer_id' => $data['leader_id']],
                    ['is_leader' => 1, 'updated_at' => now()]
                );
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Update leader failed', ['err' => $e->getMessage(), 'group' => $group]);
            return back()->withErrors(['leader' => 'Could not update leader.']);
        }

        return back()->with('success', 'Leader updated.');
    }

    public function syncMembers(Request $request, int $group)
    {
        $data = $request->validate([
            'volunteer_ids'   => 'array',
            'volunteer_ids.*' => 'integer|exists:tbl_people,id',
        ]);

        $ids = $data['volunteer_ids'] ?? [];

        $groupRow = DB::table('small_groups')->where('id', $group)->first();
        $leaderId = $groupRow->leader_id ?? null;

        // Always include leader
        if ($leaderId) {
            $ids = array_unique(array_merge($ids, [(int)$leaderId]));
        }

        // Check if any of these persons already belong to another group
        $conflicts = DB::table('small_group_members')
            ->whereIn('volunteer_id', $ids)
            ->where('group_id', '<>', $group)
            ->pluck('volunteer_id')
            ->all();

        if (!empty($conflicts)) {
            $names = DB::table('tbl_people')
                ->whereIn('id', $conflicts)
                ->pluck(DB::raw("CONCAT(firstname, ' ', lastname)"))
                ->implode(', ');

            return back()->with('error', 'These people are already in another group: '.$names);
        }

        // If no conflicts, proceed
        DB::transaction(function () use ($group, $ids, $leaderId) {
            DB::table('small_group_members')
                ->where('group_id', $group)
                ->whereNotIn('volunteer_id', $ids)
                ->delete();

            foreach ($ids as $pid) {
                DB::table('small_group_members')->updateOrInsert(
                    ['group_id' => $group, 'volunteer_id' => (int)$pid],
                    ['is_leader' => (int)((int)$leaderId === (int)$pid), 'updated_at' => now()]
                );
            }
        });

        return back()->with('success', 'Members updated.');
    }

    public function removeMember(int $group, int $person)
    {
        $leaderId = optional(DB::table('small_groups')->where('id', $group)->first())->leader_id;
        if ($leaderId && (int) $leaderId === (int) $person) {
            return back()->withErrors(['members' => 'Cannot remove the current group leader.']);
        }

        DB::table('small_group_members')
            ->where('group_id', $group)
            ->where('volunteer_id', $person)
            ->delete();

        return back()->with('success', 'Member removed.');
    }
}