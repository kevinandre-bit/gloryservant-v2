<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreativeTaskEvent extends Model
{
    protected $table = 'tbl_creative_task_events';
    public $timestamps = false;

    protected $fillable = ['task_id', 'people_id', 'event', 'meta', 'occurred_at'];

    protected $casts = [
        'meta' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(CreativeTask::class, 'task_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'people_id');
    }
}
