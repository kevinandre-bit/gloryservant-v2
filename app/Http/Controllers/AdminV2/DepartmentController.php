<?php

namespace App\Http\Controllers\AdminV2;

use App\Classes\permission;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (permission::permitted('departments') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['index']]);

        $this->middleware(function ($request, $next) {
            if (permission::permitted('departments-add') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['store']]);

        $this->middleware(function ($request, $next) {
            if (permission::permitted('departments-edit') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['update']]);

        $this->middleware(function ($request, $next) {
            if (permission::permitted('departments-delete') === 'fail') {
                abort(403);
            }

            return $next($request);
        }, ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $departments = Department::select(
                'tbl_form_department.*',
                DB::raw('(SELECT COUNT(*) FROM tbl_campus_data 
                          WHERE tbl_campus_data.department = tbl_form_department.department) as employees_count')
            )
            ->orderBy('department')
            ->paginate(100);

        return view('admin_v2.departments', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'department' => ['required', 'string', 'max:250', Rule::unique('tbl_form_department', 'department')],
            'status'     => ['required', 'in:0,1'],
        ]);

        Department::create($data);

        return redirect()->route('admin_v2.departments')->with('success', 'Department added successfully!');
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'department' => [
                'required', 'string', 'max:250',
                Rule::unique('tbl_form_department', 'department')->ignore($department->id),
            ],
            'status' => ['required', 'in:0,1'],
        ]);

        $department->update($data);

        return redirect()->route('admin_v2.departments')->with('success', 'Department updated successfully!');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('admin_v2.departments')->with('success', 'Department deleted successfully!');
    }
}
