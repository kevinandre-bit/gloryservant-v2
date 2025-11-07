<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_digital_gifts', function (Blueprint $table) {
            $table->id();
            $table->date('month')->unique();
            $table->string('theme_heading');
            $table->text('welcome_subtext')->nullable();
            $table->string('sermon_title');
            $table->date('sermon_date')->nullable();
            $table->text('sermon_description')->nullable();
            $table->string('sermon_audio_url');
            $table->string('worship_title');
            $table->string('worship_leader')->nullable();
            $table->text('worship_theme_note')->nullable();
            $table->string('worship_audio_url');
            $table->string('testimony_type')->nullable();
            $table->text('testimony_body');
            $table->string('testimony_image_path')->nullable();
            $table->string('verse_reference');
            $table->text('verse_text');
            $table->text('verse_reflection')->nullable();
            $table->json('meditation_paragraphs')->nullable();
            $table->string('artwork_image_path')->nullable();
            $table->string('artwork_caption')->nullable();
            $table->string('wallpaper_phone_url')->nullable();
            $table->string('wallpaper_desktop_url')->nullable();
            $table->text('closing_blessing')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_digital_gifts');
    }
};
