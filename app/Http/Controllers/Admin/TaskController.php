<?php
// app/Http/Controllers/Admin/TaskController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskController extends Controller {
  public function store(Request $request) {
    $data = $request->validate([
      'project_id'=>'required|uuid', 'title'=>'required|string|max:180',
      'deadline'=>'nullable|date', 'priority'=>'required|in:A,B,C,D'
    ]);
    $task = Task::create(['id'=>Str::uuid(), 'completed'=>false] + $data);
    
    if ($request->expectsJson()) {
      return response()->json([
        'success' => true,
        'task' => [
          'id' => $task->id,
          'title' => $task->title,
          'deadline' => $task->deadline ? $task->deadline->format('M j, Y') : null,
          'deadline_raw' => $task->deadline ? $task->deadline->format('Y-m-d') : null,
          'priority' => $task->priority,
          'completed' => $task->completed
        ]
      ]);
    }
    
    return back()->with('success','Task added');
  }
  public function update(Request $request, Task $task) {
    $data = $request->validate([
      'title'=>'sometimes|string|max:180',
      'deadline'=>'sometimes|nullable|date',
      'priority'=>'sometimes|in:A,B,C,D'
    ]);
    $task->update($data);
    return response()->json(['success'=>true]);
  }
  public function toggle(Task $task) {
    $task->update(['completed'=>!$task->completed]);
    if (request()->expectsJson()) {
      return response()->json(['success'=>true]);
    }
    return back();
  }
  public function destroy(Task $task) {
    $task->delete();
    if (request()->expectsJson()) {
      return response()->json(['success'=>true]);
    }
    return back()->with('success','Task deleted');
  }
  
  public function addToFocus(Request $request, Task $task) {
    $data = $request->validate(['focus_date'=>'required|date']);
    
    try {
      $session = $task->sessions()->firstOrCreate([
        'focus_date' => $data['focus_date']
      ]);
      
      return response()->json([
        'success' => true,
        'session' => $session,
        'task_id' => $task->id,
        'focus_date' => $data['focus_date']
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'error' => $e->getMessage()
      ], 500);
    }
  }
  
  public function removeFromFocus(Task $task, $date) {
    $task->sessions()->where('focus_date', $date)->delete();
    return response()->json(['success'=>true]);
  }
}