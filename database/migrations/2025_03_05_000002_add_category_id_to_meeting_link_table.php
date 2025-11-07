<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('meeting_link', 'category_id')) {
            Schema::table('meeting_link', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')
                    ->nullable()
                    ->after('description');

                $table->foreign('category_id')
                    ->references('id')
                    ->on('tbl_meeting_category')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('meeting_link', 'category_id')) {
            Schema::table('meeting_link', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            });
        }
    }
};
