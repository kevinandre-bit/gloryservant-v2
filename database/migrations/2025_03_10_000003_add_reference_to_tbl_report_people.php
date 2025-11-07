<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add reference column
        Schema::table('tbl_report_people', function (Blueprint $table) {
            $table->integer('reference')->nullable()->after('idno');
            $table->unique('reference');
        });

        // 2. Populate reference from tbl_campus_data
        DB::statement("
            UPDATE tbl_report_people rp
            JOIN tbl_campus_data cd ON rp.idno = cd.idno
            SET rp.reference = cd.reference
            WHERE cd.reference IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_report_people', function (Blueprint $table) {
            $table->dropUnique(['reference']);
            $table->dropColumn('reference');
        });
    }
};
