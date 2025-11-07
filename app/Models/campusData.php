<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampusData extends Model
{
    protected $table = 'tbl_campus_data';
    protected $guarded = [];
    public $timestamps = true;

    public function person()
    {
        return $this->belongsTo(Person::class, 'reference');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    /**
     * "Current" = latest record by start_date (no status column assumed)
     */
    public function scopeCurrent($q)
    {
        // Do NOT reference a non-existent 'status' column
        return $q->orderBy('start_date', 'desc');
    }
}