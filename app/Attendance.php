<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{   
    protected $table = 'meeting_attendance';

    protected $fillable = [
        'user_id',
        'idno',
        'employee',
        'meeting',
        'meeting_code',
        'meeting_type',
        'meeting_date', // ✅ Add this
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
