<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_creative_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('tbl_creative_requests')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'review', 'completed', 'on_hold', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->unsignedInteger('estimated_minutes')->nullable();
            $table->date('due_at')->nullable();
            $table->timestamps();

            $table->index(['request_id', 'status']);
            $table->index(['priority', 'due_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_creative_tasks');
    }
};
