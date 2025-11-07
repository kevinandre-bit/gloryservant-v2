<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReportSetupController extends Controller
{
    /* ===================== SETUP INDEX (with tabs) ===================== */

    public function index(Request $request)
    {
        // People list for assignments filter + creation
        $people = DB::table('tbl_report_people')
            ->orderBy('last_name')->orderBy('first_name')->get();

        // Assignments (with optional filters)
        $assignmentsQuery = DB::table('tbl_report_assignments as a')
            ->join('tbl_report_people as p','p.id','=','a.person_id')
            ->join('tbl_report_metrics as m','m.id','=','a.metric_id')
            ->leftJoin('tbl_report_categories as c','c.id','=','m.category_id')
            ->select(
                'a.id',
                'a.starts_on','a.ends_on',
                'p.first_name as person_first','p.last_name as person_last',
                'm.name as metric_name',
                DB::raw('COALESCE(c.name, "") as category_name')
            );

        if ($request->filled('person_filter')) {
            $assignmentsQuery->where('a.person_id', $request->person_filter);
        }
        if ($request->filled('date_filter')) {
            $d = $request->date_filter;
            $assignmentsQuery
                ->where(function($q) use ($d){
                    $q->whereNull('a.starts_on')->orWhere('a.starts_on','<=',$d);
                })
                ->where(function($q) use ($d){
                    $q->whereNull('a.ends_on')->orWhere('a.ends_on','>=',$d);
                });
        }

        // Base lists
        $categories = DB::table('tbl_report_categories')->orderBy('name')->get();
        $teams      = DB::table('tbl_report_teams')->orderBy('name')->get();

        // Status Sets and their Options (grouped)
        $statusSets = DB::table('tbl_report_status_sets')->orderBy('name')->get();
        $options = DB::table('tbl_report_status_options')
            ->orderBy('set_id')->orderBy('sort')->orderBy('label')
            ->get();

        $optionsBySet = [];
        foreach ($options as $o) {
            $optionsBySet[$o->set_id][] = $o;
        }

        // Metrics (joined to category + optional status set)
        $metrics = DB::table('tbl_report_metrics as m')
            ->leftJoin('tbl_report_categories as c', 'c.id','=','m.category_id')
            ->leftJoin('tbl_report_status_sets as s','s.id','=','m.status_set_id')
            ->orderBy('m.name')
            ->get([
                'm.id','m.name','m.category_id','m.value_type','m.status_set_id',
                'c.name as category_name', 's.name as set_name'
            ]);

        // Counts for tab badges
        $counts = [
            'categories'   => $categories->count(),
            'status_sets'  => $statusSets->count(),
            'metrics'      => $metrics->count(),
            'teams'        => $teams->count(),
            'people'       => $people->count(),
            'assignments'  => DB::table('tbl_report_assignments')->count(),
        ];
	        $metrics = DB::table('tbl_report_metrics')
	    ->orderBy('name')
	    ->get([
	        'id',
	        'name',
	        DB::raw("COALESCE(value_mode, CASE WHEN status_set_id IS NULL THEN 'scale' ELSE 'status_set' END) as value_mode"),
	        'status_set_id',
	        'weight',
	        'active',
	    ]);
        return view('admin.reports.setup', [
            'categories'   => $categories,
            'metrics'      => $metrics,
            'teams'        => $teams,
            'people'       => $people,
            'assignments'  => $assignmentsQuery
                                ->orderBy('person_last')->orderBy('metric_name')->get(),
            // NEW for status system:
            'statusSets'   => $statusSets,
            'optionsBySet' => $optionsBySet,
            'counts'       => $counts,
        ]);
    }

    /* ===================== PEOPLE (unchanged) ===================== */

    public function storePerson(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:120',
            'last_name'  => 'required|string|max:120',
            'idno'       => 'required|string|max:80|unique:tbl_report_people,idno',
            'team_id'    => 'required|exists:tbl_report_teams,id',
            'status'     => 'nullable|in:active,inactive',
        ]);

        DB::table('tbl_report_people')->insert([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'idno'       => $data['idno'],
            'team_id'    => $data['team_id'],
            'status'     => $data['status'] ?? 'active',
            'created_by' => auth()->user()->idno ?? 'SYS',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Person created.');
    }

    public function editPerson($id)
    {
        $person = DB::table('tbl_report_people')->where('id', $id)->first();
        abort_unless($person, 404);

        $teams = DB::table('tbl_report_teams')->orderBy('name')->get();

        return view('admin.reports.people_edit', compact('person','teams'));
    }

    public function updatePerson(Request $request, $id)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:120',
            'last_name'  => 'required|string|max:120',
            'idno'       => 'required|string|max:80|unique:tbl_report_people,idno,'.$id,
            'team_id'    => 'required|exists:tbl_report_teams,id',
            'status'     => 'nullable|in:active,inactive',
        ]);

        DB::table('tbl_report_people')->where('id', $id)->update([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'idno'       => $data['idno'],
            'team_id'    => $data['team_id'],
            'status'     => $data['status'] ?? 'active',
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.reports.setup')->with('success', 'Person updated.');
    }

    public function destroyPerson($id)
    {
        DB::table('tbl_report_people')->where('id', $id)->delete();
        return back()->with('success', 'Person deleted.');
    }

    /* ===================== ASSIGNMENTS (unchanged logic) ===================== */

    public function storeAssignmentsBulk(Request $request)
    {
        $data = $request->validate([
            'person_id'  => 'required|exists:tbl_report_people,id',
            'metrics'    => 'required|array|min:1',
            'metrics.*'  => 'integer|exists:tbl_report_metrics,id',
            'starts_on'  => 'nullable|date',
            'ends_on'    => 'nullable|date|after_or_equal:starts_on',
            'active'     => 'nullable|boolean',
        ]);

    $personId  = (int)$data['person_id'];
        $metricIds = array_values(array_unique($data['metrics']));
        $startsOn  = $data['starts_on'] ?? null;
        $endsOn    = $data['ends_on']   ?? null;
        $active    = $data['active']    ?? 0;

        $existing = DB::table('tbl_report_assignments')
            ->where('person_id', $personId)
            ->whereIn('metric_id', $metricIds)
            ->get(['metric_id','starts_on','ends_on'])
            ->map(fn($r) => [
                'metric_id' => (int)$r->metric_id,
                'starts_on' => $r->starts_on ? (string)$r->starts_on : null,
                'ends_on'   => $r->ends_on   ? (string)$r->ends_on   : null,
            ])->toArray();

        $existsKey = [];
        foreach ($existing as $ex) {
            $k = $ex['metric_id'].'|'.($ex['starts_on'] ?? 'null').'|'.($ex['ends_on'] ?? 'null');
            $existsKey[$k] = true;
        }

        $now  = now();
        $user = auth()->user();
        $rows = [];

        foreach ($metricIds as $mid) {
            $k = $mid.'|'.($startsOn ?? 'null').'|'.($endsOn ?? 'null');
            if (isset($existsKey[$k])) {
                continue;
            }
            $rows[] = [
                'person_id'  => $personId,
                'metric_id'  => (int)$mid,
                'starts_on'  => $startsOn,
                'ends_on'    => $endsOn,
                'active'     => $active,
                'created_by' => $user->idno ?? 'SYS',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            DB::table('tbl_report_assignments')->insert($rows);
            return back()->with('success', 'Created '.count($rows).' assignment(s).');
        }

        return back()->with('success', 'Nothing to add (duplicates were skipped).');
    }

    /* ===================== CATEGORIES (unchanged) ===================== */

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:120|unique:tbl_report_categories,name',
            'description' => 'nullable|string',
            'active'      => 'nullable|boolean',
        ]);
        DB::table('tbl_report_categories')->insert([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'active'      => $data['active'] ?? 1,
            'created_by'  => auth()->user()->idno ?? 'SYS',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        return back()->with('success', 'Category created.');
    }

    public function editCategory($id)
    {
        $cat = DB::table('tbl_report_categories')->find($id);
        abort_unless($cat, 404);

        return view('admin.reports.edit_category', ['cat' => $cat]);
    }

    public function updateCategory(Request $request, $id)
    {
        $cat = DB::table('tbl_report_categories')->find($id);
        abort_unless($cat, 404);

        $data = $request->validate([
            'name'        => 'required|string|max:120|unique:tbl_report_categories,name,'.$id,
            'description' => 'nullable|string',
            'active'      => 'nullable|boolean',
        ]);

        DB::table('tbl_report_categories')->where('id', $id)->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'active'      => $data['active'] ?? 0,
            'updated_at'  => now(),
        ]);

        return redirect()->route('admin.reports.setup')->with('success', 'Category updated.');
    }

    public function destroyCategory($id)
    {
        DB::table('tbl_report_categories')->where('id', $id)->delete();
        return back()->with('success', 'Category deleted.');
    }

    /* ===================== STATUS SETS & OPTIONS (NEW) ===================== */

    public function storeStatusSet(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:160|unique:tbl_report_status_sets,name',
            'description' => 'nullable|string',
            'active'      => 'nullable|boolean',
        ]);

        DB::table('tbl_report_status_sets')->insert([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'active'      => $data['active'] ?? 1,
            'created_by'  => auth()->user()->idno ?? 'SYS',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('success', 'Status set created.');
    }

    public function destroyStatusSet($id)
    {
        // optional: enforce no metrics tied to this set before delete
        DB::table('tbl_report_status_sets')->where('id',$id)->delete();
        DB::table('tbl_report_status_options')->where('set_id',$id)->delete();
        return back()->with('success', 'Status set deleted.');
    }

    public function storeStatusOption(Request $request)
    {
        $data = $request->validate([
            'set_id' => 'required|exists:tbl_report_status_sets,id',
            'code'   => 'required|string|max:80',
            'label'  => 'required|string|max:160',
            'weight' => 'required|numeric|min:0|max:100',
            'color'  => 'nullable|string|max:16',
            'sort'   => 'nullable|integer',
            'active' => 'nullable|boolean',
        ]);

        DB::table('tbl_report_status_options')->insert([
            'set_id' => $data['set_id'],
            'code'   => strtoupper($data['code']),
            'label'  => $data['label'],
            'weight' => (float)$data['weight'],
            'color'  => $data['color'] ?? null,
            'sort'   => $data['sort'] ?? 0,
            'active' => $data['active'] ?? 1,
            'created_by' => auth()->user()->idno ?? 'SYS',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Status option added.');
    }

    public function destroyStatusOption($id)
    {
        DB::table('tbl_report_status_options')->where('id',$id)->delete();
        return back()->with('success', 'Status option deleted.');
    }

    /* ===================== METRICS (UPDATED) ===================== */

    /** CREATE METRIC (simple) */
    // CREATE
public function storeMetric(Request $request)
{
    $data = $request->validate([
        'name'          => 'required|string|max:160',
        'value_mode'    => 'required|in:status_set,scale',
        'status_set_id' => 'nullable|integer',            // only used when value_mode = status_set
        'weight'        => 'nullable|numeric|min:0',
        'active'        => 'nullable|boolean',
        'category_id'   => 'required|exists:tbl_report_categories,id', // <— NEW
    ]);

    DB::table('tbl_report_metrics')->insert([
        'name'          => $data['name'],
        'value_mode'    => $data['value_mode'],
        'status_set_id' => $data['value_mode'] === 'status_set' ? ($data['status_set_id'] ?: null) : null,
        'weight'        => $data['weight'] ?? 1,
        'active'        => $data['active'] ?? 1,
        'category_id'   => (int)$data['category_id'],  // <— NEW
        'created_by'    => auth()->user()->idno ?? 'SYS',
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    return back()->with(['success' => 'Metric created.', 'pane' => 'metrics']);
}

// UPDATE
public function updateMetric(Request $request, $id)
{
    $metric = DB::table('tbl_report_metrics')->find($id);
    abort_unless($metric, 404);

    $data = $request->validate([
        'name'          => 'required|string|max:160',
        'value_mode'    => 'required|in:status_set,scale',
        'status_set_id' => 'nullable|integer',
        'weight'        => 'nullable|numeric|min:0',
        'active'        => 'nullable|boolean',
        'category_id'   => 'required|exists:tbl_report_categories,id', // <— NEW
    ]);

    DB::table('tbl_report_metrics')->where('id', $id)->update([
        'name'          => $data['name'],
        'value_mode'    => $data['value_mode'],
        'status_set_id' => $data['value_mode'] === 'status_set' ? ($data['status_set_id'] ?: null) : null,
        'weight'        => $data['weight'] ?? 1,
        'active'        => $data['active'] ?? 0,
        'category_id'   => (int)$data['category_id'],  // <— NEW
        'updated_at'    => now(),
    ]);

    return redirect()->route('admin.reports.setup')->with(['success' => 'Metric updated.', 'pane' => 'metrics']);
}

    /** EDIT METRIC – small edit view or modal (just the essentials) */
    public function editMetric($id)
    {
        $metric = DB::table('tbl_report_metrics')->find($id);
        abort_unless($metric, 404);

        $statusSets = DB::table('tbl_report_status_sets')
            ->where('active',1)
            ->orderBy('name')
            ->get(['id','name']);

        return view('admin.reports.edit_metric', compact('metric','statusSets'));
    }

  

    public function destroyMetric($id)
    {
        DB::table('tbl_report_metrics')->where('id',$id)->delete();
        return back()->with('success', 'Metric deleted.');
    }
    /* ===================== TEAMS (unchanged) ===================== */

    public function storeTeam(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:160|unique:tbl_report_teams,name',
            'description' => 'nullable|string',
            'active'      => 'nullable|boolean',
        ]);
        DB::table('tbl_report_teams')->insert([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'active'      => $data['active'] ?? 1,
            'created_by'  => auth()->user()->idno ?? 'SYS',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        return back()->with('success', 'Team created.');
    }

    public function editTeam($id)
    {
        $team = DB::table('tbl_report_teams')->find($id);
        abort_unless($team, 404);
        return view('admin.reports.edit_team', ['team' => $team]);
    }

    public function updateTeam(Request $request, $id)
    {
        $team = DB::table('tbl_report_teams')->find($id);
        abort_unless($team, 404);

        $data = $request->validate([
            'name'        => 'required|string|max:160|unique:tbl_report_teams,name,'.$id,
            'description' => 'nullable|string',
            'active'      => 'nullable|boolean',
        ]);

        DB::table('tbl_report_teams')->where('id',$id)->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'active'      => $data['active'] ?? 0,
            'updated_at'  => now(),
        ]);

        return redirect()->route('admin.reports.setup')->with('success', 'Team updated.');
    }

    public function destroyTeam($id)
    {
        DB::table('tbl_report_teams')->where('id',$id)->delete();
        return back()->with('success', 'Team deleted.');
    }

    /* ===================== (kept from your original) ENTRY VIEW HOOK ===================== */

    public function teamEntry($teamId)
    {
        $team    = DB::table('tbl_report_teams')->where('id', $teamId)->first();
        abort_if(!$team, 404);

        $teams   = DB::table('tbl_report_teams')->get();

        $members = DB::table('tbl_report_people')
            ->where('team_id', $teamId)
            ->where(function($q){ $q->whereNull('status')->orWhere('status','active'); })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // ACTIVE assignments
        $assignments = DB::table('tbl_report_assignments as a')
            ->join('tbl_report_metrics as m', 'm.id','=','a.metric_id')
            ->join('tbl_report_categories as c', 'c.id','=','m.category_id')
            ->leftJoin('tbl_report_status_sets as s', 's.id','=','m.status_set_id')
            ->select(
                'a.person_id',
                'm.id as metric_id',
                'm.name as metric_name',
                'm.value_type',
                'm.status_set_id',
                'c.name as category_name',
                's.name as set_name'
            )
            ->where(function($q){
                $q->whereNull('a.ends_on')->orWhere('a.ends_on','>=', now()->toDateString());
            })
            ->orderBy('m.name')
            ->get()
            ->groupBy('person_id');

        $categories = DB::table('tbl_report_categories')->get();

        return view('admin.reports.entry', [
            'team'        => $team,
            'teams'       => $teams,
            'members'     => $members,
            'assignments' => $assignments,
            'categories'  => $categories,
        ]);
    }
}