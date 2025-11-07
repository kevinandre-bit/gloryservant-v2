<?php

namespace App\Http\Controllers\RadioDashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class TechnicianRegistryController extends Controller
{
   public function index()
{
    // latest campus row per person
    $latestCampusSub = DB::table('tbl_campus_data')
        ->select('reference', DB::raw('MAX(id) as max_id'))
        ->groupBy('reference');

    $campusJoin = DB::table('tbl_campus_data as cd')
        ->joinSub($latestCampusSub, 'lcd', function ($join) {
            $join->on('cd.reference', '=', 'lcd.reference')
                 ->on('cd.id', '=', 'lcd.max_id');
        })
        ->select('cd.reference', 'cd.campus', 'cd.department');

    // schedule summary per person (non-archived)
    $schedSub = DB::table('tbl_people_schedules')
        ->where('archive', 0)
        ->select(
            'reference',
            DB::raw("GROUP_CONCAT(CONCAT(label,' ', intime, '-', outime) ORDER BY id SEPARATOR ' | ') as sched_summary")
        )
        ->groupBy('reference');

    $technicians = DB::table('tbl_people as t')
        ->join('users as u', 'u.reference', '=', 't.id')
        ->join('users_roles as r', 'r.id', '=', 'u.role_id')
        ->leftJoinSub($campusJoin, 'camp', function ($j) { $j->on('camp.reference','=','t.id'); })
        ->leftJoinSub($schedSub, 'ps',   function ($j) { $j->on('ps.reference','=','t.id'); })

        // 👇 make it case-insensitive
        ->whereRaw('UPPER(r.role_name) = ?', ['RADIO OPERATOR'])

        ->select(
            't.id as person_id',
            'u.id as user_id',
            'u.name',
            'u.email',
            't.mobileno as phone',
            DB::raw('COALESCE(camp.campus, "") as campus'),
            DB::raw('COALESCE(camp.department, "") as department'),
            DB::raw('COALESCE(ps.sched_summary, "") as schedule_summary')
        )
        ->orderBy('u.name')
        ->get();

    return view('radio_dashboard.admin.techs_index', compact('technicians'));
}

    /** Bulk-add selected Radio Operator users as technicians */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        // Keep only users that are actually Radio Operators
        $validIds = DB::table('users')
            ->whereIn('id', $data['user_ids'])
            ->where('user_role', 'Radio Operator') // <- role check
            ->pluck('id')
            ->all();

        if (empty($validIds)) {
            return back()->withErrors(['user_ids' => 'No valid Radio Operator selected.']);
        }

        $rows = collect($validIds)->map(function ($uid) {
            return [
                'user_id'   => (int) $uid,
                'home_base' => null,
                'on_call'   => 0,
                'created_at'=> now(),
                'updated_at'=> now(),
            ];
        })->all();

        DB::table('radio_technicians')->upsert($rows, ['user_id'], ['updated_at']);

        return redirect()
            ->route('radio.admin.techs.index')
            ->with('ok', 'Technicians added successfully.');
    }

    public function update(Request $request, $technicianId)
    {
        $request->validate([
            'on_call'   => 'nullable|boolean',
            'home_base' => 'nullable|string|max:120',
        ]);

        DB::table('radio_technicians')
            ->where('id', $technicianId)
            ->update([
                'on_call'    => $request->boolean('on_call'),
                'home_base'  => $request->input('home_base'),
                'updated_at' => now(),
            ]);

        return back()->with('ok', 'Technician updated.');
    }

    public function destroy($technicianId)
    {
        DB::table('radio_technicians')->where('id', $technicianId)->delete();
        return back()->with('ok', 'Technician removed.');
    }
}