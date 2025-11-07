<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add nullable earned_at column and backfill values from awarded_at if present
        Schema::table('tbl_creative_user_badges', function (Blueprint $table) {
            if (! Schema::hasColumn('tbl_creative_user_badges', 'earned_at')) {
                $table->timestamp('earned_at')->nullable()->after('awarded_at');
            }
        });

        // Backfill earned_at from awarded_at where earned_at is null
        try {
            DB::table('tbl_creative_user_badges')
                ->whereNull('earned_at')
                ->whereNotNull('awarded_at')
                ->update(['earned_at' => DB::raw('awarded_at')]);
        } catch (\Exception $e) {
            // swallow exceptions during migrations on read-only or odd environments;
            // the column has been added so manual backfill can be run if needed.
        }
    }

    public function down()
    {
        Schema::table('tbl_creative_user_badges', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_creative_user_badges', 'earned_at')) {
                $table->dropColumn('earned_at');
            }
        });
    }
};
