<?php

namespace App\Services;

use App\Models\CreativePointsLedger;
use App\Models\CreativeTask;

class PointsService
{
    public function awardTaskPoints($taskId, $peopleId)
    {
        $task = CreativeTask::find($taskId);
        if (!$task) return;

        $key = "points:task_completed:{$taskId}:{$peopleId}";
        
        if (CreativePointsLedger::where('idempotency_key', $key)->exists()) {
            return;
        }

        $basePoints = 10;
        $priorityBonus = match($task->priority) {
            'high' => 5,
            'urgent' => 10,
            default => 0
        };

        CreativePointsLedger::create([
            'people_id' => $peopleId,
            'points' => $basePoints,
            'reason' => 'task_completed',
            'ref_table' => 'tbl_creative_tasks',
            'ref_id' => $taskId,
            'idempotency_key' => $key,
        ]);

        if ($priorityBonus > 0) {
            CreativePointsLedger::create([
                'people_id' => $peopleId,
                'points' => $priorityBonus,
                'reason' => 'priority_bonus',
                'ref_table' => 'tbl_creative_tasks',
                'ref_id' => $taskId,
                'idempotency_key' => "{$key}:bonus",
            ]);
        }
    }

    public function awardAttendancePoints($peopleId, $minutes, $date)
    {
        $key = "points:attendance:{$peopleId}:{$date}";
        
        if (CreativePointsLedger::where('idempotency_key', $key)->exists()) {
            return 0;
        }

        $points = floor($minutes / 30);
        if ($points > 0) {
            CreativePointsLedger::create([
                'people_id' => $peopleId,
                'points' => $points,
                'reason' => 'time_logged',
                'ref_table' => 'tbl_people_attendance',
                'ref_id' => null,
                'idempotency_key' => $key,
            ]);
        }
        
        return $points;
    }

    public function getTotalPoints($peopleId)
    {
        return CreativePointsLedger::where('people_id', $peopleId)->sum('points');
    }
}
