<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['id','key','group','description'];
    public $incrementing = false;      // we preserve numeric IDs from old system
    protected $keyType = 'int';
}