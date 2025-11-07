<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_creative_task_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tbl_creative_tasks')->onDelete('cascade');
            $table->unsignedInteger('people_id');
            $table->enum('role', ['owner', 'reviewer'])->default('owner');
            $table->unsignedTinyInteger('allocation_percent')->nullable();
            $table->timestamp('assigned_at')->useCurrent();

            $table->unique(['task_id', 'people_id']);
            $table->foreign('people_id')->references('id')->on('tbl_people')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_creative_task_assignments');
    }
};
