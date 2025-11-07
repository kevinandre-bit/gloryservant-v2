<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Copy the mapping from app/Classes/permission.php (ID => slug)
        $perms = [
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
        705 => 'employee-birthdays-reports',
        706 => 'employee-leaves',
        707 => 'employee-list-report',
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

    // ===== RADIO OPS =====
    20  => 'radio-ops',

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
        ];

        $now = now();
        foreach ($perms as $id => $key) {
            DB::table('permissions')->updateOrInsert(
                ['id' => (int)$id],
                ['key' => $key, 'group' => strtok($key, '-'), 'updated_at' => $now, 'created_at' => $now]
            );
        }
    }
}