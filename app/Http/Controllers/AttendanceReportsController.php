<?php
// app/Http/Controllers/DevotionReportController.php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use App\Classes\permission;


class AttendanceReportsController extends Controller
{
    
    public function index()
    {
        // 1) Feature‐level permission
        if (permission::permitted('employee-attendance') === 'fail') {
            return redirect()->route('denied');
        }

        // 2) Lists of strings for the filters
        $campuses   = DB::table('tbl_form_campus')
                           ->distinct()
                           ->pluck('campus')    // gives you [ "TG DELMAS", "ANOTHER CAMPUS", ... ]
                           ->filter()           // drop null
                           ->values()
                           ->toArray();

        $ministries = DB::table('tbl_campus_data')
                           ->distinct()
                           ->pluck('ministry')
                           ->filter()
                           ->values()
                           ->toArray();

        // 3) Who’s the user & what’s their role name?
        $user       = auth()->user();
        $roleName   = DB::table('users_roles')
                        ->where('id', $user->role_id)
                        ->value('role_name') 
                     ?? '';
        $role       = strtoupper(str_replace('-', ' ', trim($roleName)));  

        // 4) Their own campus/dept (fallback into tbl_campus_data if empty on the user record)
        $campusData     = DB::table('tbl_campus_data')
                              ->where('reference', $user->reference)
                              ->first();

        $usercampus     = $user->campus    ?: optional($campusData)->campus;
        $userministry  = $user->ministry ?: optional($campusData)->ministry;

        // 5) Render
        return view('admin.reports.report-employee-attendance', compact(
            'campuses','ministries','role','usercampus','userministry','role'
        ));
    }
    
public function getData(Request $request)
{
    try {
        $campus    = trim($request->input('campus', ''));
        $ministry = trim($request->input('ministry', ''));
        $start      = $request->input('start_date');
        $end        = $request->input('end_date');

        $startDate = Carbon::parse($start)->startOfDay();
        $endDate   = Carbon::parse($end)->endOfDay();

        // ────── Join attendance with employee and org data ──────
        $query = DB::table('tbl_people_attendance as a')
            ->leftJoin('tbl_people as p',      'a.reference', '=', 'p.id')
            ->leftJoin('tbl_campus_data as cd', 'p.id', '=', 'cd.reference')
            ->leftJoin('tbl_form_campus as fc','cd.campus',   '=', 'fc.campus')
            ->select(
                'a.date',
                'p.firstname',
                'p.lastname',
                'cd.ministry',
                'fc.campus',
                'a.timein',
                'a.timeout',
                'a.totalhours',
                'a.status_timein',
                'a.status_timeout'
            )
            ->whereBetween('a.date', [$startDate, $endDate]);

        // 1) campus filter (same as before)
        if (!empty($campus)) {
            $query->where('cd.campus', 'LIKE', "%{$campus}%");
        }

        // 2) ministry filter (handle “Overall Communication” as group of sub‐ministries)
        if (!empty($ministry)) {
            if ($ministry === 'Communication') {
                $commministries = [
                    'ADMIN',
                    'Graphic Design',
                    'Video Editing',
                    'SEO',
                    'Volunteer Care',
                    'Social Media',
                    'Audio Editing',
                    'Radio_TV',
                ];
                $query->whereIn('cd.ministry', $commministries);
            } else {
                $query->where('cd.ministry', 'LIKE', "%{$ministry}%");
            }
        }

        $results = $query->get();

        return response()->json([
            'data' => $results
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'file'  => $e->getFile(),
            'line'  => $e->getLine(),
        ], 500);
    }
}


}