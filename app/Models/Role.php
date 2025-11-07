<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'users_roles'; // PDF page 50  [oai_citation:5‡u276774975_gloryservant.pdf](file-service://file-4ibWuCbfzvndHQ6spgJ9EN)
    protected $fillable = ['role_name','state'];

    public function permissions()
    {
        return $this->hasMany(\App\Models\UserPermission::class, 'role_id');
    }
}