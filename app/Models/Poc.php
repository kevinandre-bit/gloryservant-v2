<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poc extends Model
{
    protected $fillable = ['name','role','phone','email','station_id'];

    public function station() {
        return $this->belongsTo(Station::class);
    }
}