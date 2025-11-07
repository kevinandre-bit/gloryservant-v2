<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_creative_user_badges', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('people_id');
            $table->foreignId('badge_id')->constrained('tbl_creative_badges')->onDelete('cascade');
            $table->timestamp('awarded_at')->useCurrent();

            $table->unique(['people_id', 'badge_id']);
            $table->foreign('people_id')->references('id')->on('tbl_people')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_creative_user_badges');
    }
};
