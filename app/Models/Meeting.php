<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
