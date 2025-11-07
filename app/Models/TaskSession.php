<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TaskSession extends Model {
  protected $table = 'focus_task_sessions';
  protected $fillable = ['task_id', 'focus_date', 'notes'];
  protected $casts = ['focus_date' => 'date'];
  
  public function task() { 
    return $this->belongsTo(Task::class); 
  }
}
