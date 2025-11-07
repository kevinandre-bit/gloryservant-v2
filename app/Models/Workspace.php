<?php
// app/Models/Workspace.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class Workspace extends Model {
  protected $table = 'focus_workspaces';
  protected $fillable = ['name', 'user_id'];
  public $incrementing = false;
  protected $keyType = 'string';
  
  protected static function boot() {
    parent::boot();
    static::creating(function ($model) {
      if (empty($model->{$model->getKeyName()})) {
        $model->{$model->getKeyName()} = Str::uuid()->toString();
      }
    });
  }
  
  public function projects() { return $this->hasMany(Project::class); }
  public function owner() { return $this->belongsTo(User::class, 'user_id', 'idno'); }
  public function sharedUsers() { return $this->belongsToMany(User::class, 'focus_workspace_user', 'workspace_id', 'user_id', 'id', 'idno'); }
  
  public function hasAccess(string $userIdno): bool {
    return $this->user_id == $userIdno || $this->sharedUsers()->where('focus_workspace_user.user_id', $userIdno)->exists();
  }
}