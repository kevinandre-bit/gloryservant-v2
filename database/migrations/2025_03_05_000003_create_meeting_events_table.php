<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('meeting_events')) {
            Schema::create('meeting_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('meeting_link_id');
                $table->string('title')->nullable();
                $table->date('meeting_date')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->enum('frequency', ['once', 'weekly', 'biweekly', 'monthly', 'quarterly', 'custom'])
                      ->default('once');
                $table->json('frequency_meta')->nullable();
                $table->dateTime('expires_at')->nullable();
                $table->string('video_url', 500)->nullable();
                $table->enum('meeting_type', ['meeting', 'training'])->default('meeting');
                $table->json('campus_group')->nullable();
                $table->json('ministry_group')->nullable();
                $table->json('dept_group')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();

                $table->foreign('meeting_link_id')
                      ->references('id')->on('meeting_link')
                      ->cascadeOnDelete();

                $table->foreign('created_by')
                      ->references('id')->on('users')
                      ->nullOnDelete();

                $table->foreign('updated_by')
                      ->references('id')->on('users')
                      ->nullOnDelete();

                $table->index(['meeting_link_id', 'meeting_date']);
                $table->index(['meeting_type', 'meeting_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_events');
    }
};
