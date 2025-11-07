<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE tbl_creative_tasks MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    public function down()
    {
        // Cannot reverse this change
    }
};