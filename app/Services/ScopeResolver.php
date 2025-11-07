<?php

namespace App\Services;

use App\Models\UserRoleAssignment;
use Illuminate\Support\Collection;

class ScopeResolver
{
    /**
     * Return normalized scopes the user has for a given permission key or id.
     * For v1, just return all assignments; later you can filter by capability.
     */
    public function effectiveScopesForUser(int $userId): array
    {
        /** @var Collection $assignments */
        $assignments = UserRoleAssignment::query()
            ->where('user_id', $userId)
            ->where(function($q){
                $now = now();
                $q->whereNull('active_from')->orWhere('active_from','<=',$now);
            })
            ->where(function($q){
                $now = now();
                $q->whereNull('active_until')->orWhere('active_until','>=',$now);
            })
            ->get();

        $scopes = [
            'global'     => false,
            'campus'     => [],
            'department' => [],
            'ministry'   => [],
            'team'       => [],
        ];

        foreach ($assignments as $a) {
            if ($a->scope_type === 'global') { $scopes['global'] = true; continue; }
            $scopes[$a->scope_type][] = (int)$a->scope_id;
        }
        // de-dup
        foreach (['campus','department','ministry','team'] as $k) { $scopes[$k] = array_values(array_unique($scopes[$k])); }
        return $scopes;
    }
}