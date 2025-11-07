<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Standardize all idno columns to VARCHAR(50)
        
        Schema::table('tbl_campus_data', function (Blueprint $table) {
            $table->string('idno', 50)->default('')->change();
        });

        Schema::table('meeting_attendance', function (Blueprint $table) {
            $table->string('idno', 50)->nullable()->change();
        });

        Schema::table('tbl_report_people', function (Blueprint $table) {
            $table->string('idno', 50)->change();
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->string('idno', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original sizes
        Schema::table('tbl_campus_data', function (Blueprint $table) {
            $table->string('idno', 255)->default('')->change();
        });

        Schema::table('meeting_attendance', function (Blueprint $table) {
            $table->string('idno', 191)->nullable()->change();
        });

        Schema::table('tbl_report_people', function (Blueprint $table) {
            $table->string('idno', 64)->change();
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->string('idno', 50)->nullable()->change();
        });
    }
};
