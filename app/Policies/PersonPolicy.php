<?php

namespace App\Policies;

use App\Models\User;
use App\Services\CapabilityService;
use App\Services\ScopeResolver;
use Illuminate\Support\Facades\DB;

class PersonPolicy
{
    public function view(User $user): bool
    {
        // check feature access by id or key
        $ok = app(CapabilityService::class)->roleHasPermission((int)$user->role_id, /* id of 'people.view' */ 202);
        return (bool) $ok;
    }

    public function update(User $user, $person): bool
    {
        // Must have edit capability
        $capOk = app(CapabilityService::class)->roleHasPermission((int)$user->role_id, /* 'people.edit' id */ 203);
        if (! $capOk) return false;

        // Resolve user scopes
        $scopes = app(ScopeResolver::class)->effectiveScopesForUser($user->id);
        if ($scopes['global']) return true;

        // Fetch person's org info (campus/ministry/department) from DB if not provided as object
        $personId = is_object($person) ? (int)($person->id ?? 0) : (int)$person;
        if ($personId <= 0) {
            return false;
        }

        $org = DB::table('tbl_campus_data')
            ->where('reference', $personId)
            ->select('campus_id','campus','ministry','department')
            ->first();

        if (! $org) {
            // If no org row, deny by default to be safe
            return false;
        }

        // If scopes are empty (no global, no campus/ministry/department), deny by default
        $hasAnyScope = (count($scopes['campus']) || count($scopes['ministry']) || count($scopes['department']));
        if (! $hasAnyScope) return false;

        // Check matches where scopes are defined
        $campusOk = true; $ministryOk = true; $deptOk = true;
        if (!empty($scopes['campus'])) {
            // Try match by campus_id, then fallback to campus text
            $campusOk = in_array((int)($org->campus_id ?? 0), $scopes['campus'], true)
                || in_array((string)($org->campus ?? ''), array_map('strval', $scopes['campus']), true);
        }
        if (!empty($scopes['ministry'])) {
            $ministryOk = in_array((string)($org->ministry ?? ''), array_map('strval', $scopes['ministry']), true);
        }
        if (!empty($scopes['department'])) {
            $deptOk = in_array((string)($org->department ?? ''), array_map('strval', $scopes['department']), true);
        }

        return $campusOk && $ministryOk && $deptOk;
    }
}
