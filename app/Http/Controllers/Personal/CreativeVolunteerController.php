<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Classes\table;
use DB;
use Illuminate\Http\Request;

class CreativeVolunteerController extends Controller
{
    public function index()
    {
        $peopleId = auth()->user()->reference;

        $myTasks = DB::table('tbl_creative_tasks as t')
            ->join('tbl_creative_task_assignments as a', 't.id', '=', 'a.task_id')
            ->join('tbl_creative_requests as r', 't.request_id', '=', 'r.id')
            ->where('a.people_id', $peopleId)
            ->select('t.*', 'r.title as request_title', 'r.request_type', 'r.description as request_description', 'r.requester_ministry', 'r.desired_due_at')
            ->orderBy('t.created_at', 'desc')
            ->get();

        $myPoints = DB::table('tbl_creative_points_ledger')
            ->where('people_id', $peopleId)
            ->sum('points');

        $myBadges = DB::table('tbl_creative_user_badges as ub')
            ->join('tbl_creative_badges as b', 'ub.badge_id', '=', 'b.id')
            ->where('ub.people_id', $peopleId)
            ->select('b.*', 'ub.awarded_at')
            ->orderBy('ub.awarded_at', 'desc')
            ->get();

        $recentPoints = DB::table('tbl_creative_points_ledger')
            ->where('people_id', $peopleId)
            ->orderBy('occurred_at', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'total_tasks' => $myTasks->count(),
            'completed_tasks' => $myTasks->where('status', 'completed')->count(),
            'active_tasks' => $myTasks->filter(fn($t) => in_array($t->status, ['pending', 'in_progress']))->count(),
            'total_points' => $myPoints,
            'badges_earned' => $myBadges->count(),
        ];

        return view('personal.creative.dashboard', compact('myTasks', 'myBadges', 'recentPoints', 'stats'));
    }

    public function show($taskId)
    {
        $peopleId = auth()->user()->reference;

        $task = DB::table('tbl_creative_tasks as t')
            ->join('tbl_creative_task_assignments as a', 't.id', '=', 'a.task_id')
            ->join('tbl_creative_requests as r', 't.request_id', '=', 'r.id')
            ->where('t.id', $taskId)
            ->where('a.people_id', $peopleId)
            ->select('t.*', 'r.title as request_title', 'r.description as request_description', 'r.form_data', 'r.request_type', 'r.priority as request_priority', 'r.requester_ministry', 'r.desired_due_at', 'a.role', 'a.allocation_percent')
            ->first();

        if (!$task) {
            abort(404);
        }

        $comments = DB::table('tbl_creative_task_comments as c')
            ->join('tbl_people as p', 'c.people_id', '=', 'p.id')
            ->where('c.task_id', $taskId)
            ->select('c.*', 'p.firstname', 'p.lastname')
            ->orderBy('c.created_at', 'desc')
            ->get();

        return view('personal.creative.show', compact('task', 'comments'));
    }

    public function updateStatus(Request $request, $taskId)
    {
        $peopleId = auth()->user()->reference;

        $assignment = DB::table('tbl_creative_task_assignments')
            ->where('task_id', $taskId)
            ->where('people_id', $peopleId)
            ->first();

        if (!$assignment) {
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        }

        $validStatuses = ['pending', 'in_progress', 'review', 'completed'];
        $newStatus = $request->status;

        if (!in_array($newStatus, $validStatuses)) {
            return response()->json(['success' => false, 'message' => 'Invalid status'], 400);
        }

        DB::table('tbl_creative_tasks')
            ->where('id', $taskId)
            ->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

        if ($request->comment) {
            DB::table('tbl_creative_task_comments')->insert([
                'task_id' => $taskId,
                'people_id' => $peopleId,
                'comment' => $request->comment,
                'created_at' => now()
            ]);
        }

        if ($newStatus === 'completed') {
            // Use PointsService to award points (handles idempotency and priority logic)
            app(\App\Services\PointsService::class)->awardTaskPoints($taskId, $peopleId);

            // After awarding points, check for any badges to award
            app(\App\Services\BadgeService::class)->checkAndAwardBadges($peopleId);
        }

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }
}
