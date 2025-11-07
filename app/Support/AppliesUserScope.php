<?php

namespace App\Support;

use App\Services\ScopeResolver;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

trait AppliesUserScope
{
    public function scopeWithinUserScopes(Builder $q, User $user, string $entity): Builder
    {
        $scopes = app(ScopeResolver::class)->effectiveScopesForUser($user->id);

        if ($scopes['global']) return $q;

        // Map entity → columns present in your schema (PDF cites below)
        switch ($entity) {
            case 'people':
                // Your data ties through tbl_campus_data (campus_id, ministry, department)  [oai_citation:7‡u276774975_gloryservant.pdf](file-service://file-4ibWuCbfzvndHQ6spgJ9EN)
                // Here you’d join to tbl_campus_data and filter by IDs from $scopes.
                // Pseudocode until joins are standardized:
                // $q->join('tbl_campus_data','tbl_people.id','=','tbl_campus_data.reference')
                //   ->when($scopes['campus'], fn($x)=>$x->whereIn('tbl_campus_data.campus_id', $scopes['campus']))
                //   ->when($scopes['ministry'], fn($x)=>$x->whereIn('tbl_campus_data.ministry', $scopes['ministry']))
                //   ->when($scopes['department'], fn($x)=>$x->whereIn('tbl_campus_data.department', $scopes['department']));
                return $q; // implement joins in your models/repositories
            case 'attendance':
                // For meeting_attendance: campus/ministry/dept columns exist directly (page 12)  [oai_citation:8‡u276774975_gloryservant.pdf](file-service://file-4ibWuCbfzvndHQ6spgJ9EN)
                // $q->when($scopes['campus'], fn($x)=>$x->whereIn('campus', $allowedCampusNamesOrIds));
                return $q;
            default:
                return $q;
        }
    }
}