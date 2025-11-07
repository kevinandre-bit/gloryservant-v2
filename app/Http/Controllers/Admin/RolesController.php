<?php

namespace App\Http\Controllers\Admin;
use DB;
use App\Classes\table;
use App\Classes\permission;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;

class RolesController extends Controller
{
    public function index(Request $request) 
    {
        if (permission::permitted('roles') == 'fail') {
            return redirect()->route('denied');
        }

        $roles = table::roles()->get();
        return view('admin.employee-roles', compact('roles'));
    }

    public function add(Request $request) 
    {
        if (permission::permitted('roles-add') == 'fail') {
            return redirect()->route('denied');
        }

        $v = $request->validate([
            'role_name' => 'required|max:100',
            'state' => 'required|max:20',
        ]);

        $role_name = mb_strtoupper($request->role_name);
        $state = $request->state;

        table::roles()->insert([
            [
                'role_name' => $role_name,
                'state' => $state
            ],
        ]);

        return redirect('roles')->with('success', trans("New user role has been added!"));
    }

    public function delete($id, Request $request) 
    {
        if (permission::permitted('roles-delete') == 'fail') {
            return redirect()->route('denied');
        }

        table::roles()->where('id', $id)->delete();

        return redirect('roles')->with('success', trans("User role has been deleted!"));
    }

    public function get(Request $request) 
    {
        if (permission::permitted('roles-edit') == 'fail') {
            return redirect()->route('denied');
        }

        $id = $request->id;
        $data = table::roles()->where('id', $id)->get();

        foreach ($data as $d) {
            $id = $d->id;
            $role_name = $d->role_name;
            $state = $d->state;
            $scope_level = $d->scope_level ?? 'all';
        }

        return response()->json([
            "id" => Crypt::encryptString($id),
            "role_name" => $role_name,
            "state" => $state,
            "scope_level" => $scope_level,
        ]);
    }

    public function update(Request $request) 
    {
        if (permission::permitted('roles-edit') == 'fail') {
            return redirect()->route('denied');
        }

        $v = $request->validate([
            'id' => 'required|max:200',
            'role_name' => 'required|max:100',
            'state' => 'required|max:20',
            'scope_level' => 'required|in:all,campus,ministry,department',
        ]);

        $id = Crypt::decryptString($request->id);
        $role_name = mb_strtoupper($request->role_name);
        $state = $request->state;
        $scope_level = $request->scope_level;

        table::roles()->where('id', $id)->update([
            'role_name' => $role_name,
            'state' => $state,
            'scope_level' => $scope_level
        ]);

        return redirect('roles')->with('success', trans("User role has been updated!"));
    }

    public function editperm($id) 
    {
        if (permission::permitted('roles-permission') == 'fail') {
            return redirect()->route('denied');
        }

        $data = table::permissions()->where('role_id', $id)->pluck('perm_id');
        $role = table::roles()->where('id', $id)->first();
        $e_id = Crypt::encryptString($id);

        return view('admin.edits.edit-permissions', [
            'd' => $data->toArray(),
            'id' => $e_id,
            'role' => $role
        ]);
    }

    public function updateperm(Request $request) 
    {
        if (permission::permitted('roles-permission') == 'fail') {
            return redirect()->route('denied');
        }

        $v = $request->validate([
            'perms' => 'array|max:255',
            'role_id' => 'required|max:200',
            'scope_level' => 'required|in:all,campus,ministry,department',
        ]);

        $perms = $request->perms;
        $role_id = Crypt::decryptString($request->role_id);
        $scope_level = $request->scope_level;

        table::permissions()->where('role_id', $role_id)->delete();

        if (isset($perms)) {
            foreach ($perms as $perm) {
                table::permissions()->insert([
                    [
                        'role_id' => $role_id,
                        'perm_id' => $perm
                    ],
                ]);
            }
        }

        table::roles()->where('id', $role_id)->update(['scope_level' => $scope_level]);

        return redirect('roles')->with('success', trans("User permission has been updated!"));
    }
}