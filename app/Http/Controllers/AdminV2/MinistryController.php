<?php

namespace App\Http\Controllers\AdminV2;

use App\Classes\permission;
use App\Http\Controllers\Controller;
use App\Models\Ministry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Department; // add this

class MinistryController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (permission::permitted('ministries') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['index']]);

        $this->middleware(function ($request, $next) {
            if (permission::permitted('ministries-add') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['store', 'update']]);

        $this->middleware(function ($request, $next) {
            if (permission::permitted('ministries-delete') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['destroy', 'destroyMany']]);
    }

    public function index(Request $request)
    {
        $ministries = Ministry::select(
                'tbl_form_ministry.*',
                DB::raw("(SELECT COUNT(DISTINCT reference)
                          FROM tbl_campus_data
                          WHERE tbl_campus_data.ministry = tbl_form_ministry.ministry) AS employees_count")
            )
            ->orderBy('ministry')
            ->paginate(20);

        // get department names (strings) to populate the selects
        $departmentNames = Department::orderBy('department')
            ->pluck('department') // collection of strings
            ->toArray();

        return view('admin_v2.ministry', [
            'ministries'      => $ministries,
            'departmentNames' => $departmentNames,
        ]);
    }

    public function store(Request $request)
    {
        // only allow values that exist in tbl_form_department
        $allowed = Department::pluck('department')->toArray();

        $data = $request->validate([
            'ministry'   => ['required','string','max:250', Rule::unique('tbl_form_ministry','ministry')],
            'department' => ['required','string','max:250', Rule::in($allowed)],
            'status'     => ['required','in:0,1'],
        ]);

        Ministry::create($data);
        return redirect()->route('admin_v2.ministry')->with('success','Ministry added successfully!');
    }

    public function update(Request $request, Ministry $ministry)
    {
        $allowed = Department::pluck('department')->toArray();

        $data = $request->validate([
            'ministry'   => ['required','string','max:250', Rule::unique('tbl_form_ministry','ministry')->ignore($ministry->id)],
            'department' => ['required','string','max:250', Rule::in($allowed)],
            'status'     => ['required','in:0,1'],
        ]);

        $ministry->update($data);
        return redirect()->route('admin_v2.ministry')->with('success','Ministry updated successfully!');
    }
    public function destroyMany(Request $request)
{
    $data = Validator::make($request->all(), [
        'ids'   => ['required','array','min:1'],
        'ids.*' => ['integer','exists:tbl_form_ministry,id'],
    ])->validate();

    $count = Ministry::whereIn('id', $data['ids'])->delete();

    return redirect()
        ->route('admin_v2.ministry')
        ->with('success', $count.' ministry record(s) deleted.');
}

// (Optional small typo fix in your single delete)
public function destroy(Ministry $ministry)
{
    $ministry->delete();
    return redirect()->route('admin_v2.ministry')->with('success','Ministry deleted successfully!');
}
}
