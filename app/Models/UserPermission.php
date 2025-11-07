<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    // your table (screenshot shows `users_permissions`)
    protected $table = 'users_permissions';

    protected $fillable = [
        'role_id',    // present in your table (can be null)
        'user_id',
        'perm_id',
        'allow',     // boolean: 1 = allow, 0 = deny
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'allow' => 'boolean',
    ];

    public $timestamps = true;
}