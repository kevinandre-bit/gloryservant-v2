<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateRolePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // users_permissions: id, role_id, perm_id  (PDF page 49)  [oai_citation:4‡u276774975_gloryservant.pdf](file-service://file-4ibWuCbfzvndHQ6spgJ9EN)
        $rows = DB::table('users_permissions')->select('role_id', 'perm_id')->get();
        $now = now();

        foreach ($rows as $row) {
            if ($row->role_id && $row->perm_id) {
                DB::table('role_permissions')->updateOrInsert(
                    ['role_id' => (int)$row->role_id, 'permission_id' => (int)$row->perm_id],
                    ['created_at' => $now, 'updated_at' => $now]
                );
            }
        }
    }
}