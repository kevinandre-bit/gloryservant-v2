<?php

namespace App\Http\Controllers\AdminV2;

use App\Classes\permission;
use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CampusController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (permission::permitted('campus') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['index']]);

        $this->middleware(function ($request, $next) {
            if (permission::permitted('campus-add') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['store', 'update']]);

        $this->middleware(function ($request, $next) {
            if (permission::permitted('campus-delete') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['destroy', 'destroyMany']]);
    }

    public function index(Request $request)
    {
        $campus = Campus::select(
                'tbl_form_campus.*',
                // Count DISTINCT people references linked to this campus (string match on campus column)
                DB::raw("(SELECT COUNT(DISTINCT reference)
                          FROM tbl_campus_data
                          WHERE tbl_campus_data.campus = tbl_form_campus.campus) AS employees_count")
            )
            ->orderBy('campus')
            ->paginate(100);

        return view('admin_v2.campus', compact('campus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'campus' => ['required','string','max:250', Rule::unique('tbl_form_campus','campus')],
            'status' => ['required','in:0,1'],
        ]);

        Campus::create($data);

        return redirect()->route('admin_v2.campus')->with('success','Campus added successfully!');
    }

    public function update(Request $request, Campus $campus)
    {
        $data = $request->validate([
            'campus' => ['required','string','max:250', Rule::unique('tbl_form_campus','campus')->ignore($campus->id)],
            'status' => ['required','in:0,1'],
        ]);

        $campus->update($data);

        return redirect()->route('admin_v2.campus')->with('success','Campus updated successfully!');
    }

    public function destroy(Campus $campus)
    {
        $campus->delete();
        return redirect()->route('admin_v2.campus')->with('success','Campus deleted successfully!');
    }

    
}
