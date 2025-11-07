<?php

namespace App\Services;

use App\Classes\permission as PermissionMap;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class CapabilityService
{
    // Does the user's role set include this permission id?
    public function roleHasPermission(int $roleId, int $permId): bool
    {
        if ($roleId <= 0) {
            return false;
        }

        return DB::table('users_permissions')
            ->where('role_id', $roleId)
            ->whereNull('user_id')
            ->where('perm_id', $permId)
            ->exists();
    }

    // If you prefer to check by key instead of id:
    public function roleHasPermissionKey(int $roleId, string $permKey): bool
    {
        $permId = Permission::query()->where('key', $permKey)->value('id');
        if (! $permId) {
            $permId = array_search($permKey, PermissionMap::$perms, true);
        }

        return $permId ? $this->roleHasPermission($roleId, (int) $permId) : false;
    }

    public function userOverride(int $userId, int $permId): ?bool
    {
        $record = DB::table('users_permissions')
            ->where('user_id', $userId)
            ->where('perm_id', $permId)
            ->first();

        if (! $record) {
            return null;
        }

        if ($record->allow === null) {
            return null;
        }

        return (bool) $record->allow;
    }

    public function userHasPermission(int $userId, int $roleId, int $permId): bool
    {
        $override = $this->userOverride($userId, $permId);
        if ($override !== null) {
            return $override;
        }

        return $this->roleHasPermission($roleId, $permId);
    }

    public function userHasPermissionKey(int $userId, int $roleId, string $permKey): bool
    {
        $permId = Permission::query()->where('key', $permKey)->value('id');
        if (! $permId) {
            $permId = array_search($permKey, PermissionMap::$perms, true);
        }

        return $permId ? $this->userHasPermission($userId, $roleId, (int) $permId) : false;
    }
}
