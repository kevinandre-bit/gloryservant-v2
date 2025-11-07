<?php
// app/Models/Task.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model {
  protected $table = 'focus_tasks';
  protected $fillable=['project_id','title','deadline','completed','priority'];
  public $incrementing = false;
  protected $keyType = 'string';
  protected $casts = ['deadline'=>'date','completed'=>'bool'];
  
  protected static function boot() {
    parent::boot();
    static::creating(function ($model) {
      if (empty($model->{$model->getKeyName()})) {
        $model->{$model->getKeyName()} = Str::uuid()->toString();
      }
    });
  }
  
  public function project() { return $this->belongsTo(Project::class); }
  public function sessions() { return $this->hasMany(TaskSession::class); }
  
  public function getEffectivePriorityAttribute() {
    return $this->project->getEffectivePriority($this->priority);
  }
  
  public function isWeeklyFocus() {
    $effectivePri = $this->effective_priority;
    $isHighPri = in_array($effectivePri, ['A', 'B']);
    $isDueSoon = $this->deadline && $this->deadline->diffInDays(now()) <= 7;
    return !$this->completed && $isHighPri && $isDueSoon;
  }
}