<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingAttendance extends Model
{
    protected $table = 'meeting_attendance';     // your table name
    protected $guarded = [];                     // or list fillables
    public $timestamps = true;                   // if created_at/updated_at exist
}