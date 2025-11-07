<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRoleScopesSeeder extends Seeder
{
    public function run()
    {
        DB::table('users_roles')->where('role_name', 'CAMPUS POC')->update(['scope_level' => 'campus']);
        DB::table('users_roles')->where('role_name', 'CAMPUS ADMINISTRATOR')->update(['scope_level' => 'campus']);
        DB::table('users_roles')->where('role_name', 'MINISTRY LEADER')->update(['scope_level' => 'ministry']);
        DB::table('users_roles')->where('role_name', 'MINISTRY OVERSEER')->update(['scope_level' => 'ministry']);
        DB::table('users_roles')->where('role_name', 'MINISTRY CORE TEAM')->update(['scope_level' => 'ministry']);
        
        echo "✅ Role scopes updated:\n";
        echo "   - CAMPUS POC → campus\n";
        echo "   - CAMPUS ADMINISTRATOR → campus\n";
        echo "   - MINISTRY LEADER → ministry\n";
        echo "   - MINISTRY OVERSEER → ministry\n";
        echo "   - MINISTRY CORE TEAM → ministry\n";
        echo "   - All other roles → all (default)\n";
    }
}
