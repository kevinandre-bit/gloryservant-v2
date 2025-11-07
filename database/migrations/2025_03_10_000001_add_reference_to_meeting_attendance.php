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
        Schema::table('meeting_attendance', function (Blueprint $table) {
            $table->integer('reference')->nullable()->after('user_id');
            $table->index('reference');
        });

        // 2. Populate reference from users table (where user_id exists)
        DB::statement("
            UPDATE meeting_attendance ma
            JOIN users u ON ma.user_id = u.id
            SET ma.reference = u.reference
            WHERE ma.user_id IS NOT NULL AND u.reference IS NOT NULL
        ");

        // 3. Populate reference from tbl_campus_data (where user_id is null but idno exists)
        DB::statement("
            UPDATE meeting_attendance ma
            JOIN tbl_campus_data cd ON ma.idno = cd.idno
            SET ma.reference = cd.reference
            WHERE ma.user_id IS NULL AND ma.idno IS NOT NULL AND cd.reference IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_attendance', function (Blueprint $table) {
            $table->dropIndex(['reference']);
            $table->dropColumn('reference');
        });
    }
};
