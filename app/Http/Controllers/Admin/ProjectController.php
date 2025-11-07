<?php
// app/Http/Controllers/Admin/ProjectController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller {
  public function store(Request $request) {
    $data = $request->validate([
      'workspace_id'=>'required|uuid', 'name'=>'required|string|max:160',
      'deadline'=>'nullable|date', 'priority'=>'required|in:A,B,C,D'
    ]);
    try {
      $workspace = Workspace::findOrFail($data['workspace_id']);
      abort_unless($workspace->hasAccess(auth()->user()->idno), 403);
      Project::create([
        'id' => Str::uuid(),
        'user_id' => auth()->user()->idno,
        ...$data
      ]);
      return back()->with('success','Project created');
    } catch (ModelNotFoundException $e) {
      return back()->with('error', 'Workspace not found');
    } catch (\Exception $e) {
      return back()->with('error', 'Failed to create project');
    }
  }
  public function destroy(Project $project) {
    abort_unless($project->user_id == auth()->user()->idno, 403);
    $project->delete();
    return back()->with('success','Project deleted');
  }
}