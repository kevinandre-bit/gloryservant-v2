<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tbl_meeting_category')) {
            Schema::create('tbl_meeting_category', function (Blueprint $table) {
                $table->id();
                $table->string('category', 150)->unique();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('created_by')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_meeting_category');
    }
};
