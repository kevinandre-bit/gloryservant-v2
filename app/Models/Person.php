<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'tbl_people';
    protected $guarded = [];
    public $timestamps = true;
    protected $dates = ['joining_date', 'created_at', 'updated_at'];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class, 'jobtitle_id');
    }

    public function campusData()
    {
        return $this->hasMany(CampusData::class, 'reference');
    }

    public function currentCampusData()
    {
        // latest assignment by start_date
        return $this->hasOne(CampusData::class, 'reference')->orderBy('startdate', 'desc');
    }

    public function people()
{
    return $this->hasMany(\App\Models\Person::class, 'department_id');
}

}