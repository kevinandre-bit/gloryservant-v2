<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('user_permission_overrides');
    }

    public function down(): void
    {
        // no-op: table intentionally removed
    }
};
