<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * IMPORTANT: Only run this AFTER verifying all reference columns have valid data
     */
    public function up(): void
    {
        // Add foreign key constraints for data integrity
        
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('reference')
                  ->references('id')
                  ->on('tbl_people')
                  ->onDelete('cascade');
        });

        Schema::table('tbl_campus_data', function (Blueprint $table) {
            $table->foreign('reference')
                  ->references('id')
                  ->on('tbl_people')
                  ->onDelete('cascade');
        });

        Schema::table('tbl_people_attendance', function (Blueprint $table) {
            $table->foreign('reference')
                  ->references('id')
                  ->on('tbl_people')
                  ->onDelete('cascade');
        });

        Schema::table('tbl_people_devotion', function (Blueprint $table) {
            $table->foreign('reference')
                  ->references('id')
                  ->on('tbl_people')
                  ->onDelete('cascade');
        });

        Schema::table('tbl_people_leaves', function (Blueprint $table) {
            $table->foreign('reference')
                  ->references('id')
                  ->on('tbl_people')
                  ->onDelete('cascade');
        });

        Schema::table('tbl_people_schedules', function (Blueprint $table) {
            $table->foreign('reference')
                  ->references('id')
                  ->on('tbl_people')
                  ->onDelete('cascade');
        });

        Schema::table('meeting_attendance', function (Blueprint $table) {
            $table->foreign('reference')
                  ->references('id')
                  ->on('tbl_people')
                  ->onDelete('set null');
        });

        Schema::table('tbl_report_people', function (Blueprint $table) {
            $table->foreign('reference')
                  ->references('id')
                  ->on('tbl_people')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['reference']);
        });

        Schema::table('tbl_campus_data', function (Blueprint $table) {
            $table->dropForeign(['reference']);
        });

        Schema::table('tbl_people_attendance', function (Blueprint $table) {
            $table->dropForeign(['reference']);
        });

        Schema::table('tbl_people_devotion', function (Blueprint $table) {
            $table->dropForeign(['reference']);
        });

        Schema::table('tbl_people_leaves', function (Blueprint $table) {
            $table->dropForeign(['reference']);
        });

        Schema::table('tbl_people_schedules', function (Blueprint $table) {
            $table->dropForeign(['reference']);
        });

        Schema::table('meeting_attendance', function (Blueprint $table) {
            $table->dropForeign(['reference']);
        });

        Schema::table('tbl_report_people', function (Blueprint $table) {
            $table->dropForeign(['reference']);
        });
    }
};
