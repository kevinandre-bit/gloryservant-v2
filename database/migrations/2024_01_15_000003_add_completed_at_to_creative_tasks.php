<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tbl_creative_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_creative_tasks', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('updated_at');
            }
        });
    }

    public function down()
    {
        Schema::table('tbl_creative_tasks', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};