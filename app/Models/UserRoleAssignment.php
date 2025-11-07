<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRoleAssignment extends Model
{
    protected $fillable = [
        'user_id', 'role_id', 'scope_type', 'scope_id',
        'active_from', 'active_until', 'feature_overrides'
    ];
    protected $casts = ['feature_overrides' => 'array', 'active_from'=>'datetime','active_until'=>'datetime'];
}