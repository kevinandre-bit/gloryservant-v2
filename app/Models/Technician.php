<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    protected $fillable = ['name','phone','email','status','notes'];

    public function stations() {
        return $this->belongsToMany(Station::class)->withTimestamps()->withPivot('role');
    }
}