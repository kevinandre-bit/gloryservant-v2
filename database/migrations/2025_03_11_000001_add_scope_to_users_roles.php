<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScopeToUsersRoles extends Migration
{
    public function up()
    {
        Schema::table('users_roles', function (Blueprint $table) {
            $table->enum('scope_level', ['all', 'campus', 'ministry', 'department'])
                  ->default('all')
                  ->after('state');
        });
    }

    public function down()
    {
        Schema::table('users_roles', function (Blueprint $table) {
            $table->dropColumn('scope_level');
        });
    }
}
