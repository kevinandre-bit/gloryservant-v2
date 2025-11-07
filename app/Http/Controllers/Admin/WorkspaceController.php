<?php
// app/Http/Controllers/Admin/WorkspaceController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceController extends Controller {
  public function index(Request $request) {
    try {
      $userIdno = auth()->user()->idno;
      $workspaces = Workspace::where('user_id', $userIdno)
        ->orWhereHas('sharedUsers', fn($q) => $q->where('focus_workspace_user.user_id', $userIdno))
        ->with(['projects.tasks.sessions'])->orderBy('name')->get();
      $selectedId = $request->query('ws') ?: ($workspaces->first()->id ?? null);
      
      // Calculate workload data
      $ws = $workspaces->firstWhere('id', $selectedId);
      $workloadData = $this->calculateWorkload($ws);
      
      return view('admin.workspaces.index', compact('workspaces','selectedId','workloadData'));
    } catch (\Exception $e) {
      return back()->with('error', 'Failed to load workspaces');
    }
  }
  
  private function calculateWorkload($workspace) {
    if (!$workspace) return null;
    
    $data = [
      'priority_counts' => ['A'=>0,'B'=>0,'C'=>0,'D'=>0],
      'projects' => [],
      'deadline_groups' => ['overdue'=>[],'this_week'=>[],'next_week'=>[],'upcoming'=>[]]
    ];
    
    foreach ($workspace->projects as $project) {
      $incompleteTasks = $project->tasks->where('completed', false);
      
      $projectData = [
        'id' => $project->id,
        'name' => $project->name,
        'priority' => $project->priority,
        'deadline' => $project->deadline,
        'progress' => $project->progressPct(),
        'task_count' => $project->tasks->count(),
        'incomplete_count' => $incompleteTasks->count(),
        'priority_breakdown' => ['A'=>0,'B'=>0,'C'=>0,'D'=>0]
      ];
      
      foreach ($project->tasks as $task) {
        if ($task->completed) continue;
        
        $effPriority = $project->getEffectivePriority($task->priority);
        $data['priority_counts'][$effPriority]++;
        $projectData['priority_breakdown'][$effPriority]++;
        
        // Deadline grouping
        if ($task->deadline) {
          $daysUntil = now()->diffInDays($task->deadline, false);
          if ($daysUntil < 0) {
            $data['deadline_groups']['overdue'][] = ['task'=>$task,'project'=>$project,'days'=>abs($daysUntil)];
          } elseif ($daysUntil <= 7) {
            $data['deadline_groups']['this_week'][] = ['task'=>$task,'project'=>$project,'days'=>$daysUntil];
          } elseif ($daysUntil <= 14) {
            $data['deadline_groups']['next_week'][] = ['task'=>$task,'project'=>$project,'days'=>$daysUntil];
          } else {
            $data['deadline_groups']['upcoming'][] = ['task'=>$task,'project'=>$project,'days'=>$daysUntil];
          }
        }
      }
      
      $data['projects'][] = $projectData;
    }
    
    return $data;
  }
  public function store(Request $request) {
    $data = $request->validate(['name'=>'required|string|max:120']);
    Workspace::create(['id'=>Str::uuid(), 'name'=>$data['name'], 'user_id'=>auth()->user()->idno]);
    return back();
  }
  public function destroy(Workspace $workspace) {
    abort_unless($workspace->user_id == auth()->user()->idno, 403);
    $workspace->delete();
    return back();
  }
  public function shareStore(Request $request, Workspace $workspace) {
    abort_unless($workspace->user_id == auth()->user()->idno, 403);
    $data = $request->validate(['idno'=>'required|exists:users,idno']);
    $workspace->sharedUsers()->syncWithoutDetaching([$data['idno']]);
    return back()->with('success', 'Access granted');
  }
  public function shareDestroy(Workspace $workspace, User $user) {
    abort_unless($workspace->user_id == auth()->user()->idno, 403);
    $workspace->sharedUsers()->detach($user->idno);
    return back()->with('success', 'Access removed');
  }
  public function workload(Workspace $workspace) {
    abort_unless($workspace->hasAccess(auth()->user()->idno), 403);
    $items = [];
    foreach ($workspace->projects()->with('tasks')->get() as $p) {
      foreach ($p->tasks as $t) {
        $effectivePri = $p->getEffectivePriority($t->priority);
        $isWeeklyFocus = !$t->completed && 
                        in_array($effectivePri, ['A', 'B']) && 
                        $t->deadline && 
                        $t->deadline->diffInDays(now()) <= 7;
        
        $items[] = [
          'id' => $t->id,
          'title' => $t->title,
          'completed' => $t->completed,
          'deadline' => optional($t->deadline)->toDateString(),
          'taskPriority' => $t->priority,
          'projectPriority' => $p->priority,
          'projectName' => $p->name,
          'effectivePriority' => $effectivePri,
          'isWeeklyFocus' => $isWeeklyFocus,
          'daysUntilDue' => $t->deadline ? $t->deadline->diffInDays(now(), false) : null,
        ];
      }
    }
    return response()->json($items);
  }
}