<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('volunteer_tasks', function (Blueprint $table) {
            // Change idno from INT to VARCHAR(50) to match other tables
            $table->string('idno', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteer_tasks', function (Blueprint $table) {
            // Revert back to INT (data loss possible if non-numeric values exist)
            $table->integer('idno')->nullable()->change();
        });
    }
};
