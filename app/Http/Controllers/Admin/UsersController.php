<?php

namespace App\Http\Controllers\Admin;
use DB;
use App\Classes\table;
use App\Classes\permission;
use App\Http\Requests;
use App\Models\User as UserModel;
use App\Support\PermissionCatalog;
use App\Services\CapabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Mail\NewUserWelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    /**
     * Display a list of users, roles, and employee records.
     */
    public function index()
    {
        // Check if the current user has permission to view users
        if (permission::permitted('users') == 'fail') {
            return redirect()->route('denied');
        }

        // Retrieve users with their role names
        $users_roles = table::users()
            ->join('users_roles', 'users.role_id', '=', 'users_roles.id')
            ->select('users.*', 'users_roles.role_name')
            ->get();

        // Retrieve all users, roles, and employee records
        $users = table::users()->get();
        $roles = table::roles()->get();
        $employees = table::people()->get(); 
        

        // Return the admin user management view with all data
        return view('admin.users', compact('users', 'roles', 'employees', 'users_roles'));
    }

    /**
     * Register a new user in the system.
     */
    public function register(Request $request)
    {
        // Check permission to add users
        if (permission::permitted('users-add') == 'fail') {
            return redirect()->route('denied');
        }

        // Validate incoming request data
        $v = $request->validate([
            'ref' => 'required|max:100',
            'name' => 'required|max:100',
            'email' => 'required|email|max:100',
            'role_id' => 'required|digits_between:1,99|max:2',
            'acc_type' => 'required|digits_between:1,99|max:2',
            'password' => 'required|min:8|max:100',
            'password_confirmation' => 'required|min:8|max:100',
            'status' => 'required|boolean|max:1',
        ]);

        // Assign request inputs to variables
        $ref = $request->ref;
        $name = $request->name;
        $email = $request->email;
        $role_id = $request->role_id;
        $acc_type = $request->acc_type;
        $password = $request->password;
        $password_confirmation = $request->password_confirmation;
        $status = $request->status;

        // Check if password matches confirmation
        if ($password != $password_confirmation) {
            return redirect('users')->with('error', trans("Whoops! Password confirmation does not match!"));
        }

        // Check if user already exists by email
        $is_user_exist = table::users()->where('email', $email)->count();
        if ($is_user_exist >= 1) {
            return redirect('users')->with('error', trans("Whoops! this user already exist"));
        }

        // Get employee ID number using the reference value
        $idno = table::campusdata()->where('reference', $ref)->value('idno');

        // Insert the new user into the database
        table::users()->insert([
            [
                'reference' => $ref,
                'idno' => $idno,
                'name' => $name,
                'email' => $email,
                'role_id' => $role_id,
                'acc_type' => $acc_type,
                'password' => Hash::make($password),
                'status' => $status,
            ],
        ]);

        // Security: Do NOT email plaintext passwords.
        // Optionally trigger a password reset link so the user can set their own password securely.
        try {
            Password::sendResetLink(['email' => $email]);
        } catch (\Throwable $e) {
            Log::warning('Failed to send reset link for new user', ['email' => $email, 'err' => $e->getMessage()]);
        }

        return redirect('/users')->with('success', trans("New user has been added."));
    }

    /**
     * Show the edit form for a specific user.
     */
    public function edit($id) 
    {
        // Check permission to edit users
        if (permission::permitted('users-edit') == 'fail') {
            return redirect()->route('denied');
        }

        // Retrieve the user record and list of roles
        $u = table::users()->where('id', $id)->first();
        $r = table::roles()->get();

        // Encrypt the reference value for use in forms
        $e_id = ($u->reference == null) ? 0 : Crypt::encryptString($u->reference);

        return view('admin.users', compact('u', 'r', 'e_id'));
    }

    /**
     * Update an existing user's details.
     */
    public function update(Request $request) 
    {
        // Check permission to edit users
        if (permission::permitted('users-edit') == 'fail') {
            return redirect()->route('denied');
        }

        // Validate required fields
        $v = $request->validate([
            'reference' => 'required|max:200',
            'role_id' => 'required|digits_between:1,99|max:2',
            'acc_type' => 'required|digits_between:1,99|max:2',
            'status' => 'required|boolean|max:1',
            'scope_level' => 'required|in:inherit,all,campus,ministry,department',
        ]);

        // Decrypt the reference and assign other inputs
        $ref = $request->reference;
        $role_id = $request->role_id;
        $acc_type = $request->acc_type;
        $password = $request->password;
        $password_confirmation = $request->password_confirmation;
        $status = $request->status;
        $scope_level = $request->scope_level;

        // Handle password update if provided
        if ($password !== null && $password_confirmation !== null) {
            $v = $request->validate([
                'password' => 'required|min:8|max:100',
                'password_confirmation' => 'required|min:8|max:100',
            ]);

            if ($password != $password_confirmation) {
                return redirect('users')->with('error', trans("Whoops! Password confirmation does not match!"));
            }

            // Update with new password
            table::users()->where('reference', $ref)->update([
                'role_id' => $role_id,
                'acc_type' => $acc_type,
                'status' => $status,
                'scope_level' => $scope_level,
                'password' => Hash::make($password),
            ]);
        } else {
            // Update without changing password
            table::users()->where('reference', $ref)->update([
                'role_id' => $role_id,
                'acc_type' => $acc_type,
                'status' => $status,
                'scope_level' => $scope_level,
            ]);
        }

        return redirect('users')->with('success', trans("User Account has been updated!"));
    }

    public function permissionsJson($id)
    {
        if (permission::permitted('users') == 'fail') {
            abort(403);
        }

        try {
            $user = UserModel::findOrFail($id);
            $catalog = PermissionCatalog::grouped();
            $overrides = DB::table('users_permissions')
                ->where('user_id', $user->id)
                ->get()
                ->keyBy('perm_id');
            $capability = app(CapabilityService::class);

            $permissions = [];

            foreach ($catalog as $group => $items) {
                $groupItems = [];
                foreach ($items as $item) {
                    $override = $overrides->get($item['id']);
                    $state = 'inherit';

                    if ($override) {
                        if ($override->allow === null) {
                            $state = 'inherit';
                        } else {
                            $state = $override->allow ? 'allow' : 'deny';
                        }
                    }

                    $groupItems[] = [
                        'id'        => $item['id'],
                        'key'       => $item['key'],
                        'label'     => $item['label'],
                        'state'     => $state,
                        'effective' => $capability->userHasPermission($user->id, (int) $user->role_id, $item['id']),
                        'role_has'  => $capability->roleHasPermission((int) $user->role_id, $item['id']),
                    ];
                }

                $permissions[] = [
                    'group' => $group,
                    'items' => $groupItems,
                ];
            }

            return response()->json([
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'scope_level' => $user->scope_level ?? 'inherit',
                ],
                'permissions' => $permissions,
            ]);
        } catch (\Throwable $e) {
            \Log::error('permissionsJson failed', [
                'user_id' => $id,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Unable to load permissions',
            ], 500);
        }
    }

    public function updatePermissions(Request $request, $id)
    {
        if (permission::permitted('users-edit') == 'fail') {
            return redirect()->route('denied');
        }

        $user = UserModel::findOrFail($id);

        $request->validate([
            'scope_level' => 'required|in:inherit,all,campus,ministry,department',
        ]);

        $input = $request->input('overrides', []);
        if (! is_array($input)) {
            $input = [];
        }

        $validIds = PermissionCatalog::ids();
        $validStates = ['inherit', 'allow', 'deny'];

        $filtered = [];
        foreach ($input as $permId => $state) {
            $permId = (int) $permId;
            if (! in_array($permId, $validIds, true)) {
                continue;
            }
            if (! in_array($state, $validStates, true)) {
                continue;
            }
            $filtered[$permId] = $state;
        }

        DB::transaction(function () use ($user, $filtered, $request) {
            DB::table('users')->where('id', $user->id)->update(['scope_level' => $request->scope_level]);
            DB::table('users_permissions')->where('user_id', $user->id)->delete();

            if (empty($filtered)) {
                return;
            }

            $actor = auth()->id();
            $timestamp = now();
            $rows = [];
            $roleId = $user->role_id ? (int) $user->role_id : 0;

            foreach ($filtered as $permId => $state) {
                if ($state === 'inherit') {
                    continue;
                }

                $rows[] = [
                    'role_id'    => $roleId,
                    'user_id'    => $user->id,
                    'perm_id'    => $permId,
                    'allow'      => $state === 'allow' ? 1 : 0,
                    'created_by' => $actor,
                    'updated_by' => $actor,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            if (! empty($rows)) {
                DB::table('users_permissions')->insert($rows);
            }
        });

        return redirect()->route('users')->with('success', 'User permissions updated.');
    }


public static function alertModalData()
{
    return [
        'roles' => table::roles()->get(),
        'ministries' => table::tbl_form_ministry()->get(),
        'campuses' => table::tbl_form_campus()->get(),
        'employees' => table::tbl_people()->get(),
    ];
    return view('layouts.default', AlertController::alertModalData());

}

public function updateWorkType(Request $request)
{
    // Only privileged users can change work type
    if (permission::permitted('users-edit') == 'fail') {
        return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
    }
    // Accept either 'id' or 'reference' (some parts of your app use 'reference')
    $payloadId = $request->input('id') ?? $request->input('reference');

    // Validate
    try {
        $request->validate([
            'work_type' => 'required|string|in:onsite,hybrid,remote|max:50',
        ]);
        if (empty($payloadId)) {
            throw ValidationException::withMessages(['id' => 'The id/reference field is required.']);
        }
    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors'  => $e->errors()
        ], 422);
    }

    $reference = $payloadId;
    $workType  = $request->input('work_type');

    try {
        $updated = DB::table('users')
            ->where('reference', $reference)
            ->update(['work_type' => $workType]);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Work type updated'
            ]);
        }

        // 0 rows updated — could be because record not found or same value
        return response()->json([
            'success' => false,
            'message' => 'No rows updated (record not found or value unchanged).'
        ], 200);

    } catch (\Throwable $ex) {
        Log::error('updateWorkType error: '.$ex->getMessage(), [
            'reference' => $reference,
            'work_type' => $workType,
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Server error while updating work type'
        ], 500);
    }
}
    /**
     * Delete a user by ID.
     */
    public function delete($id, Request $request)
    {
        // Check permission to delete users
        if (permission::permitted('users-delete') == 'fail') {
            return redirect()->route('denied');
        }

        // Delete the user record
        table::users()->where('id', $id)->delete();

        return redirect('users')->with('success', trans("User Account has been deleted!"));
    }
}
