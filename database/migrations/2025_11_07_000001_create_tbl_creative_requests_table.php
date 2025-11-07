<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_creative_requests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('request_type', ['video', 'graphic', 'audio', 'other'])->default('other');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['pending', 'in_progress', 'review', 'completed', 'on_hold', 'cancelled'])->default('pending');
            $table->unsignedInteger('requester_people_id');
            $table->date('desired_due_at')->nullable();
            $table->unsignedInteger('campus_id')->nullable();
            $table->unsignedInteger('ministry_id')->nullable();
            $table->unsignedInteger('department_id')->nullable();
            $table->timestamps();

            $table->index(['status', 'request_type', 'priority']);
            $table->index('desired_due_at');
            $table->index(['campus_id', 'ministry_id', 'department_id']);
            $table->foreign('requester_people_id')->references('id')->on('tbl_people')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_creative_requests');
    }
};
