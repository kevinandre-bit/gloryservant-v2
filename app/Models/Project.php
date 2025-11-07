<?php

// app/Models/Project.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model {
  protected $table = 'focus_projects';
  protected $fillable = ['workspace_id','name','deadline','priority','user_id'];
  public $incrementing = false;
  protected $keyType = 'string';
  protected $casts = ['deadline'=>'date'];
  
  protected static function boot() {
    parent::boot();
    static::creating(function ($model) {
      if (empty($model->{$model->getKeyName()})) {
        $model->{$model->getKeyName()} = Str::uuid()->toString();
      }
    });
  }
  
  public function workspace() { return $this->belongsTo(Workspace::class); }
  public function tasks() { return $this->hasMany(Task::class); }
  public function owner() { return $this->belongsTo(User::class, 'user_id', 'idno'); }
  
  public function progressPct(): int {
    $total = $this->tasks->count(); if (!$total) return 0;
    $done = $this->tasks->where('completed', true)->count();
    return (int) round(($done / $total) * 100);
  }
  
  public function getEffectivePriority($taskPriority) {
    $priorities = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4];
    $projectPri = $priorities[$this->priority] ?? 4;
    $taskPri = $priorities[$taskPriority] ?? 4;
    $effective = min($projectPri, $taskPri);
    return array_search($effective, $priorities);
  }
}