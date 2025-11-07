<?php
namespace App\Http\Controllers\Admin;

use App\Classes\permission;
use App\Classes\table;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class SchedulesController extends Controller
{
    public function index(Request $request)
    {
        if (permission::permitted('schedules') == 'fail') {
            return redirect()->route('denied');
        }

        $filters = [
            'campus'     => $request->query('campus'),
            'ministry'   => $request->query('ministry'),
            'department' => $request->query('department'),
        ];

        $campusOptions = table::campusdata()
            ->select('campus')
            ->whereNotNull('campus')
            ->where('campus', '<>', '')
            ->groupBy('campus')
            ->orderBy('campus')
            ->pluck('campus')
            ->toArray();

        $ministryOptions = table::campusdata()
            ->select('ministry')
            ->whereNotNull('ministry')
            ->where('ministry', '<>', '')
            ->groupBy('ministry')
            ->orderBy('ministry')
            ->pluck('ministry')
            ->toArray();

        $departmentOptions = table::campusdata()
            ->select('department')
            ->whereNotNull('department')
            ->where('department', '<>', '')
            ->groupBy('department')
            ->orderBy('department')
            ->pluck('department')
            ->toArray();

        $baseQuery = DB::table('tbl_people_schedules as s')
            ->leftJoin('tbl_campus_data as c', 'c.reference', '=', 's.reference')
            ->where('s.archive', 0);

        if (!empty($filters['campus'])) {
            $baseQuery->where('c.campus', $filters['campus']);
        }
        if (!empty($filters['ministry'])) {
            $baseQuery->where('c.ministry', $filters['ministry']);
        }
        if (!empty($filters['department'])) {
            $baseQuery->where('c.department', $filters['department']);
        }

        $schedules = (clone $baseQuery)
            ->select('s.*', 'c.campus', 'c.ministry', 'c.department')
            ->orderByDesc('s.created_at')
            ->get();

        $daysOfWeek = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        $weeklyCounts = [];
        foreach ($daysOfWeek as $day) {
            $clone = clone $baseQuery;
            $count = $clone
                ->where(function ($query) use ($day) {
                    $query->whereNull('s.restday')
                        ->orWhere('s.restday', '')
                        ->orWhereRaw(
                            "FIND_IN_SET(?, REPLACE(LOWER(s.restday), ' ', '')) = 0",
                            [strtolower($day)]
                        );
                })
                ->distinct()
                ->count('s.reference');

            $weeklyCounts[$day] = $count;
        }

        $employee  = table::people()->get(); // for dropdown
        $tf        = table::settings()->value('time_format');

        return view('admin.schedules', compact(
            'employee',
            'schedules',
            'tf',
            'filters',
            'campusOptions',
            'ministryOptions',
            'departmentOptions',
            'weeklyCounts'
        ));
    }

    /**
     * AJAX pre-check for conflicts before saving.
     * POST: reference(id), datefrom, dateto
     * Returns JSON: { conflict: bool, conflicts: [...] }
     */
    public function checkConflict(Request $request)
    {
        if (permission::permitted('schedules-add') == 'fail') {
            return response()->json(['error' => 'denied'], 403);
        }

        $request->validate([
            'id'       => 'required|max:20',  // reference
            'datefrom' => 'required|date',
            'dateto'   => 'required|date|after_or_equal:datefrom',
        ]);

        $reference = $request->id;
        $idno = table::campusdata()->where('reference', $reference)->value('idno');

        if (!$idno) {
            return response()->json(['conflict' => false, 'conflicts' => []]);
        }

        $overlaps = $this->findOverlappingActive($idno, $request->datefrom, $request->dateto)->get();

        return response()->json([
            'conflict'  => $overlaps->count() > 0,
            'conflicts' => $overlaps,
        ]);
    }

    public function add(Request $request)
    {
        if (permission::permitted('schedules-add') == 'fail') {
            return redirect()->route('denied');
        }

        $request->validate([
            'id'       => 'required|max:20',     // reference
            'intime'   => 'required|max:15',     // 'HH:mm'
            'outime'   => 'required|max:15',     // 'HH:mm'
            'datefrom' => 'required|date',
            'dateto'   => 'required|date|after_or_equal:datefrom',
            'restday'  => 'array',
        ]);

        $reference = $request->id;
        $idno = table::campusdata()->where('reference', $reference)->value('idno');

        // derive employee name (trust server)
        $person = table::people()->where('id', $reference)->first();
        $employeeName = $person ? mb_strtoupper(($person->lastname ?? '').', '.($person->firstname ?? '')) : mb_strtoupper($request->input('employee',''));

        $intimeVal = $request->intime; // 'HH:mm'
        $outimeVal = $request->outime; // 'HH:mm'
        $datefrom  = $request->datefrom;
        $dateto    = $request->dateto;
        $restdays  = $request->input('restday', []);

        $hoursCalc  = $this->computeWeeklyHours($intimeVal, $outimeVal, $restdays);
        $intimeDisp = date('h:i A', strtotime($intimeVal));
        $outimeDisp = date('h:i A', strtotime($outimeVal));
        $restdayStr = $restdays ? implode(', ', $restdays) : null;

        // Conflict enforcement on server
        $overlapsQ = $this->findOverlappingActive($idno, $datefrom, $dateto);
        $hasConflict = $overlapsQ->exists();

        if ($hasConflict && !$request->boolean('force')) {
            return back()->with('error', 'Conflict: another active schedule overlaps this range. Please confirm to continue.')
                         ->withInput();
        }

        DB::transaction(function() use ($overlapsQ, $reference, $idno, $employeeName, $intimeDisp, $outimeDisp, $datefrom, $dateto, $hoursCalc, $restdayStr) {

            // If forced or conflict exists: archive overlapping ones (new takes priority)
            $overlaps = $overlapsQ->get();
            if ($overlaps->count() > 0) {
                table::schedules()
                    ->whereIn('id', $overlaps->pluck('id'))
                    ->update(['archive' => 1, 'is_active' => 0]);
            }

            // Insert new schedule (becomes active below)
            table::schedules()->insert([
                'reference' => $reference,
                'idno'      => $idno,
                'employee'  => $employeeName,
                'intime'    => $intimeDisp,  // store pretty time like your legacy
                'outime'    => $outimeDisp,
                'datefrom'  => $datefrom,
                'dateto'    => $dateto,
                'hours'     => $hoursCalc,
                'restday'   => $restdayStr,
                'archive'   => 0,
            ]);

            // activate latest for this person
            $this->activateSchedule($idno);
        });

        $msg = $hasConflict
             ? 'New schedule saved. Overlapping schedules were archived and made inactive.'
             : 'New schedule added!';
        return redirect('schedules')->with('success', $msg);
    }

    public function edit($id)
    {
        if (permission::permitted('schedules-edit') == 'fail') {
            return redirect()->route('denied');
        }

        $s   = table::schedules()->where('id', $id)->first();
        $r   = $s && $s->restday ? explode(', ', $s->restday) : [];
        $e_id = ($s && $s->id) ? Crypt::encryptString($s->id) : 0;
        $tf  = table::settings()->value('time_format');

        return view('admin.schedules', compact('s','r','e_id','tf'));
    }

    public function update(Request $request)
    {
        if (permission::permitted('schedules-edit') == 'fail') {
            return redirect()->route('denied');
        }

        $request->validate([
            'id'       => 'required|max:200',
            'intime'   => 'required|max:15',  // 'HH:mm'
            'outime'   => 'required|max:15',
            'datefrom' => 'required|date',
            'dateto'   => 'required|date|after_or_equal:datefrom',
            'restday'  => 'array',
        ]);

        $id        = Crypt::decryptString($request->id);
        $row       = table::schedules()->where('id', $id)->firstOrFail();
        $idno      = $row->idno;

        $intimeVal = $request->intime;
        $outimeVal = $request->outime;
        $datefrom  = $request->datefrom;
        $dateto    = $request->dateto;
        $restdays  = $request->input('restday', []);

        $hoursCalc  = $this->computeWeeklyHours($intimeVal, $outimeVal, $restdays);
        $intimeDisp = date('h:i A', strtotime($intimeVal));
        $outimeDisp = date('h:i A', strtotime($outimeVal));
        $restdayStr = $restdays ? implode(', ', $restdays) : null;

        // Optional: conflict check on update as well (same person, other schedules)
        $overlapsQ = $this->findOverlappingActive($idno, $datefrom, $dateto)
                          ->where('id', '<>', $id);
        $hasConflict = $overlapsQ->exists();
        if ($hasConflict && !$request->boolean('force')) {
            return back()->with('error', 'Conflict: another active schedule overlaps this range. Confirm to continue.')
                         ->withInput();
        }

        DB::transaction(function() use ($id, $overlapsQ, $intimeDisp, $outimeDisp, $datefrom, $dateto, $hoursCalc, $restdayStr, $idno) {

            if ($overlapsQ->exists()) {
                $overlapIds = $overlapsQ->pluck('id');
                table::schedules()
                    ->whereIn('id', $overlapIds)
                    ->update(['archive' => 1, 'is_active' => 0]);
            }

            table::schedules()
                ->where('id', $id)
                ->update([
                    'intime'   => $intimeDisp,
                    'outime'   => $outimeDisp,
                    'datefrom' => $datefrom,
                    'dateto'   => $dateto,
                    'hours'    => $hoursCalc,
                    'restday'  => $restdayStr,
                    'archive'  => 0,
                ]);

            $this->activateSchedule($idno);
        });

        return redirect('schedules')->with('success', 'Schedule has been updated!');
    }

    public function delete($id)
    {
        if (permission::permitted('schedules-delete') == 'fail') {
            return redirect()->route('denied');
        }

        table::schedules()->where('id', $id)->delete();
        return redirect('schedules')->with('success', 'Deleted!');
    }

    public function archive($id, Request $request)
    {
        if (permission::permitted('schedules-archive') == 'fail') {
            return redirect()->route('denied');
        }

        $id = $request->id ?: $id;
        table::schedules()->where('id', $id)->update(['archive' => 1, 'is_active' => 0]);
        return redirect('schedules')->with('success', 'Schedule has been archived!');
    }

    private function activateSchedule($idno)
    {
        // Get reference from idno
        $reference = table::campusdata()->where('idno', $idno)->value('reference');
        if (!$reference) return;
        
        // Deactivate all for this person
        table::schedules()->where('reference', $reference)->update(['is_active' => 0]);
        // Activate latest non-archived
        table::schedules()
            ->where('reference', $reference)
            ->where('archive', 0)
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->update(['is_active' => 1]);
    }

    /**
     * Find overlapping non-archived schedules for a given idno and date range.
     */
    private function findOverlappingActive($idno, $from, $to)
    {
        if (!$idno) {
            return table::schedules()->whereRaw('1=0'); // empty
        }
        // Get reference from idno
        $reference = table::campusdata()->where('idno', $idno)->value('reference');
        if (!$reference) {
            return table::schedules()->whereRaw('1=0'); // empty
        }
        return table::schedules()
            ->where('reference', $reference)
            ->where('archive', 0)
            ->where(function($q) use ($from, $to){
                $q->where(function($qq) use ($from, $to) {
                    // overlap if: new.from <= existing.to AND new.to >= existing.from
                    $qq->where('datefrom', '<=', $to)
                       ->where('dateto', '>=', $from);
                });
            });
    }

    /**
     * Weekly hours = (daily hours intime→outime) × (7 − restdays). Handles overnight.
     */
    private function computeWeeklyHours(string $intime, string $outime, array $restdays): float
    {
        // Expect 'HH:mm'
        [$sh, $sm] = array_map('intval', explode(':', $intime));
        [$eh, $em] = array_map('intval', explode(':', $outime));
        $startMin = $sh * 60 + $sm;
        $endMin   = $eh * 60 + $em;
        $diff = $endMin - $startMin;
        if ($diff < 0) $diff += 24 * 60; // overnight
        $dailyHours = $diff / 60.0;
        $workDays   = max(0, 7 - count($restdays ?? []));
        return round($dailyHours * $workDays, 2);
    }
}
