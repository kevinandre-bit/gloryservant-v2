<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreativeTask extends Model
{
    protected $table = 'tbl_creative_tasks';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'request_id', 'title', 'description', 'status', 'priority', 'estimated_minutes', 'due_at'
    ];

    protected $casts = [
        'due_at' => 'date',
    ];

    public function request()
    {
        return $this->belongsTo(CreativeRequest::class, 'request_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(Person::class, 'tbl_creative_task_assignments', 'task_id', 'people_id')
            ->withPivot('role', 'allocation_percent', 'assigned_at');
    }

    public function events()
    {
        return $this->hasMany(CreativeTaskEvent::class, 'task_id');
    }

    public function attachments()
    {
        return $this->hasMany(CreativeAttachment::class, 'task_id');
    }
}
