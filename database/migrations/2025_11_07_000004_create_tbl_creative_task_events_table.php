<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_creative_task_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tbl_creative_tasks')->onDelete('cascade');
            $table->unsignedInteger('people_id')->nullable();
            $table->enum('event', ['created', 'started', 'moved_status', 'completed', 'commented', 'attached_asset'])->default('created');
            $table->json('meta')->nullable();
            $table->timestamp('occurred_at')->useCurrent();

            $table->index(['task_id', 'occurred_at']);
            $table->foreign('people_id')->references('id')->on('tbl_people')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_creative_task_events');
    }
};
