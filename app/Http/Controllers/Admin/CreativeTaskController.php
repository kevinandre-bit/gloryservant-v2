<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreativeTask;
use App\Models\CreativeRequest;
use App\Models\Person;
use App\Services\PointsService;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use DB;

class CreativeTaskController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'request_id' => 'required|exists:tbl_creative_requests,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'estimated_minutes' => 'nullable|integer|min:1',
            'due_at' => 'nullable|date|after_or_equal:today',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:tbl_people,id'
        ]);

        $task = CreativeTask::create([
            'request_id' => $data['request_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'priority' => $data['priority'],
            'estimated_minutes' => $data['estimated_minutes'] ?? null,
            'due_at' => $data['due_at'] ?? null,
            'status' => 'pending'
        ]);

        // Assign people to task
        if (!empty($data['assignees'])) {
            foreach ($data['assignees'] as $peopleId) {
                DB::table('tbl_creative_task_assignments')->insert([
                    'task_id' => $task->id,
                    'people_id' => $peopleId,
                    'role' => 'owner',
                    'assigned_at' => now()
                ]);
                
                // Log assignment event
                DB::table('tbl_creative_task_events')->insert([
                    'task_id' => $task->id,
                    'people_id' => $peopleId,
                    'event' => 'created',
                    'occurred_at' => now()
                ]);
                
                // Send notification email and in-app notification
                $person = \App\Models\Person::find($peopleId);
                if ($person) {
                    // Email notification
                    if ($person->emailaddress) {
                        \Mail::to($person->emailaddress)->send(new \App\Mail\CreativeTaskAssigned($task, $person));
                    }
                    
                    // In-app notification
                    try {
                        $user = \App\Models\User::where('reference', $peopleId)->first();
                        if ($user) {
                            $notificationId = \DB::table('notifications')->insertGetId([
                                'type' => 'creative_task_assigned',
                                'title' => 'New Creative Task Assigned',
                                'body' => "You've been assigned: {$task->title}",
                                'subject_table' => 'tbl_creative_tasks',
                                'subject_id' => $task->id,
                                'url' => "/personal/creative/tasks/{$task->id}",
                                'icon' => 'paint_brush',
                                'created_at' => now()
                            ]);
                            
                            \DB::table('notification_targets')->insert([
                                'notification_id' => $notificationId,
                                'user_id' => $user->id
                            ]);
                        }
                    } catch (\Exception $e) {
                        // Ignore notification errors
                    }
                }
            }
        }

        return redirect()->route('admin.creative.requests.show', $data['request_id'])->with('success', 'Task created and assigned successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $task = CreativeTask::findOrFail($id);
        $newStatus = $request->validate(['status' => 'required|in:pending,in_progress,review,completed,on_hold,cancelled'])['status'];
        
        $oldStatus = $task->status;
        $task->status = $newStatus;
        $task->save();

        // Log status change
        DB::table('tbl_creative_task_events')->insert([
            'task_id' => $id,
            'people_id' => auth()->user()->reference ?? null,
            'event' => 'moved_status',
            'meta' => json_encode(['from' => $oldStatus, 'to' => $newStatus]),
            'occurred_at' => now()
        ]);

        // Award points and check badges if completed
        if ($newStatus === 'completed') {
            $pointsService = new PointsService();
            $badgeService = new BadgeService();
            
            foreach ($task->assignees as $assignee) {
                $pointsService->awardTaskPoints($id, $assignee->id);
                $badgeService->checkAndAwardBadges($assignee->id);
            }
        }

        return response()->json(['success' => true, 'message' => 'Task status updated']);
    }
    
    public function update(Request $request, $id)
    {
        $task = CreativeTask::findOrFail($id);
        
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'estimated_minutes' => 'nullable|integer|min:1',
            'due_at' => 'nullable|date'
        ]);
        
        $task->update($data);
        
        return redirect()->route('admin.creative.requests.show', $task->request_id)->with('success', 'Task updated successfully');
    }
    
    public function destroy($id)
    {
        $task = CreativeTask::findOrFail($id);
        $requestId = $task->request_id;
        $task->delete();
        
        return redirect()->route('admin.creative.requests.show', $requestId)->with('success', 'Task deleted successfully');
    }
}