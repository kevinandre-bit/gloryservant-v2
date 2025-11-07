<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'bible_verses',
            'focus_projects',
            'focus_tasks',
            'focus_workspaces',
            'ht_arrondissements',
            'ht_communes',
            'ht_departements',
            'locations',
            'mr_agenda_items',
            'mr_asana_mappings',
            'mr_attendees',
            'mr_drive_settings',
            'mr_magic_links',
            'mr_metrics',
            'mr_shows',
            'mr_tasks',
            'mr_teams',
            'notification_targets',
            'password_resets',
            'personal_access_tokens',
            'playlist_exports',
            'playlist_items',
            'playlists',
            'radio_pocs',
            'radio_technician',
            'reading_plan_chapters',
            'requests',
            'settings',
            'small_group_members',
            'small_groups',
            'tbl_computers',
            'tbl_equipment',
            'tbl_form_department',
            'tbl_form_jobtitle',
            'tbl_form_leavegroup',
            'tbl_form_leavetype',
            'tbl_people_attendance',
            'tbl_people_devotion',
            'tbl_people_leaves',
            'tbl_people_schedules',
            'tbl_report_assignments',
            'tbl_report_categories',
            'tbl_report_metric_events',
            'tbl_report_metric_person_overrides',
            'tbl_report_metric_scale',
            'tbl_report_metric_status_items',
            'tbl_report_metrics',
            'tbl_report_people',
            'tbl_report_report_rollups',
            'tbl_report_status_options',
            'tbl_report_status_set_items',
            'tbl_report_status_sets',
            'tbl_report_teams',
            'tbl_report_views',
            'technicians',
            'tracks',
            'user_activity_logs',
            'users',
            'users_roles',
            'volunteer_followups',
            'volunteer_progress',
            'volunteer_tasks',
            'volunteers',
        ];

        foreach ($tables as $table) {
            try {
                // Check if table exists
                if (!DB::getSchemaBuilder()->hasTable($table)) {
                    echo "⚠️  Table {$table} does not exist, skipping...\n";
                    continue;
                }

                // Check if id column exists
                if (!DB::getSchemaBuilder()->hasColumn($table, 'id')) {
                    echo "⚠️  Table {$table} has no id column, skipping...\n";
                    continue;
                }

                // Get current column definition
                $columns = DB::select("SHOW COLUMNS FROM `{$table}` WHERE Field = 'id'");
                if (empty($columns)) {
                    continue;
                }

                $col = $columns[0];
                
                // Skip if already AUTO_INCREMENT
                if (stripos($col->Extra, 'auto_increment') !== false) {
                    echo "✓ Table {$table} already has AUTO_INCREMENT\n";
                    continue;
                }

                // Determine data type
                $type = strtoupper($col->Type);
                $unsigned = stripos($type, 'unsigned') !== false ? 'UNSIGNED' : '';
                
                // Extract base type
                if (stripos($type, 'BIGINT') !== false) {
                    $baseType = 'BIGINT';
                } elseif (stripos($type, 'INT') !== false) {
                    $baseType = 'INT';
                } else {
                    echo "⚠️  Table {$table} id column is {$type}, skipping...\n";
                    continue;
                }

                // Add AUTO_INCREMENT
                DB::statement("ALTER TABLE `{$table}` MODIFY `id` {$baseType} {$unsigned} NOT NULL AUTO_INCREMENT");
                echo "✅ Added AUTO_INCREMENT to {$table}.id\n";

            } catch (\Exception $e) {
                echo "❌ Error on table {$table}: " . $e->getMessage() . "\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot safely reverse AUTO_INCREMENT without data loss
        echo "⚠️  Reversing AUTO_INCREMENT is not recommended and not implemented.\n";
    }
};
