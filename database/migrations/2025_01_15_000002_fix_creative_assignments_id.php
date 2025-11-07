<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE tbl_creative_task_assignments MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    public function down()
    {
        // Cannot reverse this change
    }
};