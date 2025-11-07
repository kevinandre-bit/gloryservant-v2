<?php

namespace App\Classes;

use App\Models\Permission as PermissionModel;
use App\Services\CapabilityService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Auth;
 
Class permission {

    protected static $permissionTableErrorLogged = false;

    public static $perms = [
    // ===== HR / Core =====
    1   => 'dashboard',
    2   => 'employees',
        201 => 'employees-add',
        202 => 'employees-view',
        203 => 'employees-edit',
        204 => 'employees-delete',
        205 => 'employees-archive',
        206 => 'employee-list',
        207 => 'employee-birthdays',

    3   => 'attendance',
        301 => 'attendance-edit',
        302 => 'attendance-delete',

    4   => 'schedules',
        401 => 'schedules-add',
        402 => 'schedules-edit',
        403 => 'schedules-delete',
        404 => 'schedules-archive',

    5   => 'leaves',
        501 => 'leaves-add',
        502 => 'leaves-edit',
        503 => 'leaves-delete',

    7   => 'reports',
        701 => 'user-activity',
        702 => 'asana-portfolio',
        703 => 'user-accounts',
        704 => 'employee-attendance',
        706 => 'employee-leaves',
        707 => 'employee-list',
        708 => 'employee-schedule',

    8   => 'users',
        801 => 'users-add',
        802 => 'users-edit',
        803 => 'users-delete',

    9   => 'settings',
        901 => 'settings-update',

    10  => 'roles',
        1001 => 'roles-add',
        1002 => 'roles-edit',
        1003 => 'roles-permission',
        1004 => 'roles-delete',

    11  => 'campus',
        1101 => 'campus-add',
        1102 => 'campus-delete',

    12  => 'ministries',
        1201 => 'ministries-add',
        1202 => 'ministries-delete',

    13  => 'jobtitles',
        1301 => 'jobtitles-add',
        1302 => 'jobtitles-delete',

    14  => 'leavetypes',
        1401 => 'leavetypes-add',
        1402 => 'leavetypes-delete',

    15  => 'leavegroups',
        1501 => 'leavegroup-add',
        1502 => 'leavegroup-edit',
        1503 => 'leavegroup-delete',

    16  => 'sendMail',

    17  => 'meeting',
        1701 => 'meeting-links',
        1702 => 'meeting-attendance',

    18  => 'devotions',
        1801 => 'devotion-view',

    19  => 'departments',
        1901 => 'departments-add',
        1902 => 'departments-edit',
        1903 => 'departments-delete',

    // ===== RADIO OPS =====
    2000  => 'radio-ops',

        // Dashboard
        2001 => 'radio-dashboard',

        // Programming
        2100 => 'radio-programming',
            2101 => 'radio-programming-batches',
            2102 => 'radio-programming-upload',
            2103 => 'radio-programming-template',

        // Playout
        2200 => 'radio-playout',
            2201 => 'radio-playout-logs',
            2202 => 'radio-playout-deviations',

        // Technicians
        2300 => 'radio-tech',
            2301 => 'radio-tech-schedule',
            2302 => 'radio-tech-assignments',
            2303 => 'radio-tech-checkins',
            2304 => 'radio-tech-availability',

        // Maintenance
        2400 => 'radio-maintenance',
            2401 => 'radio-maintenance-tasks',
            2402 => 'radio-maintenance-calendar',

        // Monitoring
        2500 => 'radio-monitoring',
            2501 => 'radio-monitoring-source',
            2502 => 'radio-monitoring-hub',
            2503 => 'radio-monitoring-sites',

        // Reports
        2600 => 'radio-reports',
            2601 => 'radio-reports-daily-admin',
            2602 => 'radio-reports-weekly',
            2603 => 'radio-reports-daily-tech',
            2604 => 'radio-reports-daily-op',
            2605 => 'radio-reports-daily-admin-ui',
            2606 => 'radio-reports-studio',

    // ===== FINANCE =====
    30  => 'finance',
        3001 => 'finance-expenses',
        3002 => 'finance-recurring',
        3003 => 'finance-vendors',

    // ===== DIRECTORY =====
    40  => 'directory',
        4001 => 'directory-register-station',
        4002 => 'directory-register-tech',
        4003 => 'directory-register-poc',
        4004 => 'directory-inventory',

    // ===== SETTINGS =====
    50  => 'radio-settings',
        5001 => 'radio-settings-users',
        5002 => 'radio-settings-computers',
        5003 => 'radio-settings-inventory',
        5004 => 'radio-settings-support',

    // ===== REPORT SYSTEM =====
    60  => 'report-system',               // opens the Report System module root

    // Setup (create/edit)
    6001 => 'report-setup-view',      // can open the setup UI
    6002 => 'report-setup-edit',      // can create/update categories, metrics, status sets
    6003 => 'report-setup-delete',    // can delete in setup (cats/metrics/status sets)
    6004 => 'report-assignments-edit',// can create/update assignments
    6005 => 'report-assignments-delete',

    // Daily Entry (team entry modal)
    6101 => 'report-entry-view',      // can open team entry page
    6102 => 'report-entry-save',      // can save daily entries
    6103 => 'report-entry-delete',    // can clear/delete an entry

    // Analytics / Reports
    6201 => 'report-view-weekly',     // can open weekly report
    6202 => 'report-view-monthly',    // can open monthly report
    6203 => 'report-view-quarterly',  // can open quarterly report
    6204 => 'report-export',          // can export CSV/PDF

        // Wellness Followups
    7000 => 'wellness-followups',          // access page
    7001 => 'wellness-followups-store',    // create a followup
    7002 => 'wellness-followups-show',     // view specific followup

    // Admin Followups
    7010 => 'admin-followups',             // access admin followups page
    7011 => 'admin-followups-assign',      // assign followup
]; 

        /*public static function permitted($page) 
            {
                $role = \Auth::user()->role_id;
                $perms=self::$perms;
                $permid = array_search($page, $perms);
                $permcheck = Permissions::where('role_id', $role)->where('perm_id', $permid)->first();
        
                if ($permcheck==NULL) 
                {
                    return "fail";
                } else {
                    if ($permcheck->perm_id<0) 
                    {
                        return "fail";
                    } else { 
                        return "success";
                    }
                }
        }
    public static function permitted($page)
{
    // pull role_id, or null if no user
    $role = auth()->user()?->role_id;  
    // or: $role = optional(auth()->user())->role_id;

    // if no role, deny
    if ( ! $role ) {
        return "fail";
    }

    $perms     = self::$perms;
    $permid    = array_search($page, $perms);
    $permcheck = Permissions::where('role_id', $role)
                            ->where('perm_id',  $permid)
                            ->first();

    if ( ! $permcheck || $permcheck->perm_id < 0 ) {
        return "fail";
    }

    return "success";
}*/
// inside App\Classes\permission
public static function check($permid)
{
    $user = \Auth::user();
    if (! $user) {
        return 'fail';
    }

    $capability = app(CapabilityService::class);

    return $capability->userHasPermission((int) $user->id, (int) $user->role_id, (int) $permid)
        ? 'success'
        : 'fail';
}

public static function permitted($page)
{
    try {
        $user = optional(auth()->user());
        $userId = (int) ($user->id ?? 0);
        $roleId = (int) ($user->role_id ?? 0);

        if ($userId === 0 || $roleId === 0) {
            return 'fail';
        }

        // Super admin shortcut
        if ($roleId === 1) {
            return 'success';
        }

        $permId = self::resolvePermId($page);
        if ($permId === null) {
            return 'fail';
        }

        // Capability service guarded
        try {
            $capability = app(\App\Services\CapabilityService::class);
            return $capability->userHasPermission($userId, $roleId, $permId)
                ? 'success'
                : 'fail';
        } catch (\Throwable $e) {
            Log::warning('CapabilityService error in permitted()', [
                'user_id' => $userId,
                'role_id' => $roleId,
                'perm_id' => $permId,
                'msg'     => $e->getMessage(),
            ]);
            return 'fail';
        }
    } catch (\Throwable $e) {
        Log::warning('permitted() failed', ['page' => $page, 'msg' => $e->getMessage()]);
        return 'fail';
    }
}

protected static function resolvePermId(string $key): ?int
{
    try {
        $id = PermissionModel::query()->where('key', $key)->value('id');
        if ($id) {
            return (int) $id;
        }
    } catch (\Throwable $e) {
        if (! self::$permissionTableErrorLogged) {
            Log::notice('Permission lookup fell back to legacy map', [
                'key'   => $key,
                'error' => $e->getMessage(),
            ]);
            self::$permissionTableErrorLogged = true;
        }
    }

    $permid = array_search($key, self::$perms, true);

    return $permid === false ? null : (int) $permid;
}

public static function getScope()
{
    $user = auth()->user();
    if (!$user || !$user->role_id) {
        return null;
    }

    if (isset($user->scope_level) && $user->scope_level !== 'inherit') {
        return $user->scope_level;
    }

    $role = DB::table('users_roles')->where('id', $user->role_id)->first();
    return $role ? $role->scope_level : 'all';
}

public static function hasFullAccess()
{
    return self::getScope() === 'all';
}

public static function getScopeData()
{
    $user = auth()->user();
    if (!$user || !$user->reference) {
        return null;
    }

    $campusData = DB::table('tbl_campus_data')
        ->where('reference', $user->reference)
        ->first(['campus', 'ministry', 'department']);

    return $campusData ? [
        'campus' => $campusData->campus,
        'ministry' => $campusData->ministry,
        'department' => $campusData->department
    ] : null;
}

}
