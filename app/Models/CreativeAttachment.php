<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreativeAttachment extends Model
{
    protected $table = 'tbl_creative_attachments';

    protected $fillable = [
        'request_id', 'task_id', 'uploaded_by_people_id', 'disk', 'path', 'filename', 'mime', 'size_bytes'
    ];

    public function request()
    {
        return $this->belongsTo(CreativeRequest::class, 'request_id');
    }

    public function task()
    {
        return $this->belongsTo(CreativeTask::class, 'task_id');
    }

    public function uploader()
    {
        return $this->belongsTo(Person::class, 'uploaded_by_people_id');
    }
}
