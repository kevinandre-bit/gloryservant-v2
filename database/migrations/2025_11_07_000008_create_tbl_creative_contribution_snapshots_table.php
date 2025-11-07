<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_creative_contribution_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('people_id');
            $table->enum('period', ['day', 'week', 'month', 'quarter', 'year'])->default('week');
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('tasks_completed')->default(0);
            $table->unsignedInteger('minutes_logged')->default(0);
            $table->unsignedInteger('points_earned')->default(0);
            $table->timestamps();

            $table->unique(['people_id', 'period', 'period_start'], 'creative_contrib_unique');
            $table->foreign('people_id')->references('id')->on('tbl_people')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_creative_contribution_snapshots');
    }
};
