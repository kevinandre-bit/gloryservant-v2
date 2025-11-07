<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreativeRequest extends Model
{
    protected $table = 'tbl_creative_requests';

    protected $fillable = [
        'title', 'description', 'request_type', 'priority', 'status',
        'requester_people_id', 'desired_due_at', 'campus_id', 'ministry_id', 'department_id',
        'form_data', 'admin_approved', 'requester_name', 'requester_ministry', 'requester_email'
    ];

    protected $casts = [
        'desired_due_at' => 'date',
        'form_data' => 'array',
        'admin_approved' => 'boolean',
    ];

    public function requester()
    {
        return $this->belongsTo(Person::class, 'requester_people_id');
    }

    public function tasks()
    {
        return $this->hasMany(CreativeTask::class, 'request_id');
    }

    public function attachments()
    {
        return $this->hasMany(CreativeAttachment::class, 'request_id');
    }
}
