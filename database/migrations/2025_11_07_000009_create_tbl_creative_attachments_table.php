<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_creative_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->nullable()->constrained('tbl_creative_requests')->onDelete('cascade');
            $table->foreignId('task_id')->nullable()->constrained('tbl_creative_tasks')->onDelete('cascade');
            $table->unsignedInteger('uploaded_by_people_id');
            $table->string('disk', 32)->default('public');
            $table->string('path');
            $table->string('filename');
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->timestamps();

            $table->foreign('uploaded_by_people_id')->references('id')->on('tbl_people')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_creative_attachments');
    }
};
