<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_creative_points_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('people_id');
            $table->integer('points');
            $table->enum('reason', ['task_completed', 'priority_bonus', 'time_logged', 'manual_adjustment'])->default('task_completed');
            $table->string('ref_table', 64)->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->string('idempotency_key')->unique()->nullable();
            $table->timestamp('occurred_at')->useCurrent();

            $table->index(['people_id', 'occurred_at']);
            $table->foreign('people_id')->references('id')->on('tbl_people')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_creative_points_ledger');
    }
};
