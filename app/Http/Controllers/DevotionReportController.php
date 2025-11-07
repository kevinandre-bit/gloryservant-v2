<?php
// app/Http/Controllers/DevotionReportController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class DevotionReportController extends Controller
{
    public function index()
    {
        $campuses = DB::table('tbl_form_campus')
            ->distinct()
            ->pluck('campus')
            ->filter()  // remove any null/empty
            ->values()
            ->toArray();

        $ministries = DB::table('tbl_campus_data')
            ->distinct()
            ->pluck('ministry')
            ->filter()
            ->values()
            ->toArray();

        // 2. Identify current user
        $user = auth()->user();

        // 3. Look up their text role name
        $roleName = DB::table('users_roles')
            ->where('id', $user->role_id)
            ->value('role_name') 
            ?? '';
        $role = strtoupper(str_replace('-', ' ', trim($roleName)));

        // 4. Grab their own campus/ministry (fallback via campus_data)
        $campusData = DB::table('tbl_campus_data')
            ->where('reference', $user->reference)
            ->first();

        $userCampus    = $user->campus    ?: optional($campusData)->campus;
        $userministry = $user->ministry ?: optional($campusData)->ministry;

        // 5. Pass _only_ the variables the Blade needs
        return view('admin.reports.report-devotions', [
            'campuses'       => $campuses,
            'ministries'    => $ministries,
            'role'           => $role,
            'userCampus'     => $userCampus,
            'userministry' => $userministry,
        ]);
    }

    public function getData(Request $request)
    {
        try {
            // 1) Read/filter inputs
            $campus     = trim($request->input('campus', ''));      // e.g. “TG DELMAS”
            $ministry = trim($request->input('ministry', ''));  // e.g. “Overall Communication”
            $start      = $request->input('start_date');
            $end        = $request->input('end_date');

            // 2) Parse dates
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate   = Carbon::parse($end)->endOfDay();
            $daySpan   = $startDate->diffInDays($endDate) + 1;
            if ($daySpan < 1) {
                $daySpan = 1;
            }

            // 3) Build subquery: devotion counts
            $devCount = DB::table('tbl_people_devotion')
                ->select([
                    'reference',
                    DB::raw('COUNT(*) as total')
                ])
                ->whereBetween('devotion_date', [$startDate, $endDate])
                ->groupBy('reference');

            // 4) Filtered employees query
            $employeesQuery = DB::table('tbl_people as p')
                ->join('tbl_campus_data as cd', 'p.id', '=', 'cd.reference');

            // 4a) Campus filter
            if (!empty($campus)) {
                $employeesQuery->where('cd.campus', 'LIKE', "%{$campus}%");
            }

            // 4b) ministry filter
            if (!empty($ministry)) {
                if (strpos($ministry, ',') !== false) {
                    $deptArray = array_map('trim', explode(',', $ministry));
                    $employeesQuery->whereIn('cd.ministry', $deptArray);
                } else {
                    $employeesQuery->where('cd.ministry', 'LIKE', "%{$ministry}%");
                }
            }

            $filteredEmployees = (clone $employeesQuery)->count();

            $results = $employeesQuery
                ->leftJoinSub($devCount, 'dc', function($join) {
                    $join->on('p.id', '=', 'dc.reference');
                })
                ->select([
                    'p.id as person_id',
                    'p.firstname',
                    'p.lastname',
                    'cd.ministry',
                    'cd.campus',
                    DB::raw('COALESCE(dc.total, 0) as total'),
                ])
                ->orderBy('p.lastname')
                ->get();

            $data = $results->map(function($row) use ($daySpan) {
                $row->percentage = $daySpan
                    ? round((($row->total / $daySpan) * 100), 2)
                    : 0.00;
                return $row;
            });

            return response()->json([
                'data'            => $data,
                'total_employees' => $filteredEmployees,
                'days'            => $daySpan,
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ], 500);
        }
    }

    public function globalDevotionReport(Request $request)
    {
        $filterFrom       = $request->input('datefrom', '');
        $filterTo         = $request->input('dateto', '');
        $filterCampus     = $request->input('campus', '');
        $filterDept       = $request->input('ministry', '');
        $filterVolunteerName = trim($request->input('volunteer', ''));

        $q = DB::table('tbl_people_devotion as d')
            ->leftJoin('tbl_campus_data  as cd', 'd.reference', '=', 'cd.reference')
            ->leftJoin('tbl_people        as p',  'd.reference', '=', 'p.id')
            ->leftJoin('tbl_form_campus   as fc', 'cd.campus',   '=', 'fc.campus')
            ->select([
                DB::raw('DATE_FORMAT(d.devotion_date, "%Y-%m-%d") as devotion_date'),
                DB::raw('CONCAT_WS(" ", p.lastname, p.firstname) as employee'),
                'fc.campus      as campus',
                'cd.ministry  as ministry',
                'd.devotion_text',
                DB::raw('DATE_FORMAT(d.created_at, "%Y-%m-%d %H:%i:%s") as created_at'),
            ])
            ->orderBy('d.created_at', 'desc');

        if ($filterFrom !== '' && $filterTo !== '') {
            $q->whereBetween('d.devotion_date', [$filterFrom, $filterTo]);
        }

        if ($filterCampus !== '') {
            $q->where('fc.campus', $filterCampus);
        }

        if ($filterDept !== '') {
            $q->where('cd.ministry', $filterDept);
        }

        if ($filterVolunteerName !== '') {
            $q->whereRaw(
                "CONCAT_WS(' ', p.firstname, p.lastname) LIKE ?",
                ["%{$filterVolunteerName}%"]
            );
        }

        $devotions = $q->get();

        $campuses = DB::table('tbl_form_campus')
            ->distinct()
            ->pluck('campus')
            ->filter()
            ->values()
            ->toArray();

        $ministries = DB::table('tbl_campus_data')
            ->distinct()
            ->pluck('ministry')
            ->filter()
            ->values()
            ->toArray();

        $employees = DB::table('tbl_people as p')
            ->join('tbl_campus_data as cd', 'p.id', '=', 'cd.reference')
            ->where('p.employmentstatus', 'Active')
            ->select('p.id', 'p.firstname', 'p.lastname')
            ->orderBy('p.firstname')
            ->orderBy('p.lastname')
            ->get();

        if ($request->input('ajax') == '1') {
            return response()->json($devotions);
        }

        return view('admin.reports.report-global-devotions', [
            'devotions'           => $devotions,
            'campuses'            => $campuses,
            'ministries'         => $ministries,
            'employees'           => $employees,
            'filterCampus'        => $filterCampus,
            'filterDept'          => $filterDept,
            'filterVolunteerName' => $filterVolunteerName,
            'filterFrom'          => $filterFrom,
            'filterTo'            => $filterTo,
        ]);
    }
}