<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    protected $table = 'tbl_form_jobtitle';
    protected $guarded = []; // or specify fillable
    public $timestamps = true;

    public function people()
    {
        return $this->hasMany(Person::class, 'jobtitle_id');
    }
}
