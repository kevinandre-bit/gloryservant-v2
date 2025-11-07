<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\CreativeTask;
use App\Models\CreativePointsLedger;
use App\Models\CreativeBadge;
use App\Services\PointsService;
use Illuminate\Support\Facades\Auth;
use DB;

class CreativeController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $peopleId = $user->reference;
        
        // My active tasks
        $myTasks = CreativeTask::whereHas('assignees', function($q) use ($peopleId) {
            $q->where('people_id', $peopleId);
        })
        ->with(['request', 'assignees'])
        ->whereIn('status', ['pending', 'in_progress', 'review'])
        ->orderBy('due_at')
        ->limit(10)
        ->get();
        
        // Points summary
        $pointsService = new PointsService();
        $totalPoints = $pointsService->getTotalPoints($peopleId);
        
        $pointsBreakdown = CreativePointsLedger::where('people_id', $peopleId)
            ->select('reason', DB::raw('SUM(points) as total'))
            ->groupBy('reason')
            ->get()
            ->pluck('total', 'reason');
        
        // My badges
        $myBadges = DB::table('tbl_creative_user_badges')
            ->join('tbl_creative_badges', 'tbl_creative_user_badges.badge_id', '=', 'tbl_creative_badges.id')
            ->where('people_id', $peopleId)
            ->select('tbl_creative_badges.*', 'tbl_creative_user_badges.earned_at')
            ->orderBy('tbl_creative_user_badges.earned_at', 'desc')
            ->get();
        
        // Recent activity
        $recentActivity = DB::table('tbl_creative_task_events')
            ->join('tbl_creative_tasks', 'tbl_creative_task_events.task_id', '=', 'tbl_creative_tasks.id')
            ->join('tbl_creative_requests', 'tbl_creative_tasks.request_id', '=', 'tbl_creative_requests.id')
            ->where('tbl_creative_task_events.people_id', $peopleId)
            ->select('tbl_creative_task_events.*', 'tbl_creative_tasks.title as task_title', 'tbl_creative_requests.title as request_title')
            ->orderBy('occurred_at', 'desc')
            ->limit(5)
            ->get();
        
        // This month stats
        $thisMonth = [
            'tasks_completed' => DB::table('tbl_creative_task_events')
                ->where('people_id', $peopleId)
                ->where('event', 'completed')
                ->whereYear('occurred_at', now()->year)
                ->whereMonth('occurred_at', now()->month)
                ->count(),
            'points_earned' => CreativePointsLedger::where('people_id', $peopleId)
                ->whereYear('occurred_at', now()->year)
                ->whereMonth('occurred_at', now()->month)
                ->sum('points')
        ];
        
        return view('personal.creative.dashboard', compact(
            'myTasks', 'totalPoints', 'pointsBreakdown', 'myBadges', 
            'recentActivity', 'thisMonth'
        ));
    }
    
    public function updateTaskStatus($taskId)
    {
        $task = CreativeTask::findOrFail($taskId);
        $user = Auth::user();
        
        // Check if user is assigned to this task
        if (!$task->assignees->contains('id', $user->reference)) {
            abort(403, 'You are not assigned to this task');
        }
        
        $newStatus = request('status');
        $validStatuses = ['pending', 'in_progress', 'review', 'completed'];
        
        if (!in_array($newStatus, $validStatuses)) {
            return back()->with('error', 'Invalid status');
        }
        
        $task->status = $newStatus;
        $task->save();
        
        // Log the event
        DB::table('tbl_creative_task_events')->insert([
            'task_id' => $taskId,
            'people_id' => $user->reference,
            'event' => 'moved_status',
            'meta' => json_encode(['from' => $task->getOriginal('status'), 'to' => $newStatus]),
            'occurred_at' => now()
        ]);
        
        // Award points if completed
        if ($newStatus === 'completed') {
            $pointsService = new PointsService();
            $pointsService->awardTaskPoints($taskId, $user->reference);
        }
        
        return back()->with('success', 'Task status updated');
    }
    
    public function show($taskId)
    {
        $task = CreativeTask::with(['request', 'assignees', 'events'])->findOrFail($taskId);
        $user = Auth::user();
        
        // Check if user is assigned to this task
        if (!$task->assignees->contains('id', $user->reference)) {
            abort(403, 'You are not assigned to this task');
        }
        
        return view('personal.creative.show', compact('task'));
    }
}