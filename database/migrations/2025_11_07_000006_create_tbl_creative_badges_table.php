<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_creative_badges', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('criteria')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_creative_badges');
    }
};
