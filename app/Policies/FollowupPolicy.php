<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class FollowupPolicy
{
    protected function isAdmin(User $user): bool
    {
        // Make this reflect YOUR app. Covers both patterns.
        return ($user->is_admin ?? false)
            || in_array(strtolower($user->role ?? ''), ['admin','overseer']);
    }

    public function view(User $user, int $followupId): bool
    {
        if ($this->isAdmin($user)) return true;

        $row = DB::table('volunteer_followups')
            ->select('leader_id','assigned_to_id')
            ->where('id', $followupId)
            ->first();

        if (!$row) return false;

        return (int)$row->leader_id === (int)$user->id
            || (int)($row->assigned_to_id ?? 0) === (int)$user->id;
    }

    public function update(User $user, int $followupId): bool
    {
        return $this->view($user, $followupId);
    }

    public function assign(User $user, int $followupId): bool
    {
        return $this->isAdmin($user);
    }

    public function download(User $user, int $followupId): bool
    {
        return $this->view($user, $followupId);
    }
}