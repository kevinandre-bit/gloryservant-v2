<?php

namespace App\Services;

use App\Classes\table;
use DB;
use Carbon\Carbon;

class AnalyticsService
{
    public function getPerformanceMetrics($days = 30)
    {
        $startDate = now()->subDays($days);
        
        return [
            'completion_rate' => $this->getCompletionRate($startDate),
            'avg_completion_time' => $this->getAverageCompletionTime($startDate),
            'efficiency_score' => $this->getEfficiencyScore($startDate),
            'workload_trend' => $this->getWorkloadTrend($startDate),
            'top_performers' => $this->getTopPerformers($startDate),
            'bottlenecks' => $this->getBottlenecks($startDate)
        ];
    }

    private function getCompletionRate($startDate)
    {
        $total = table::creativeTasks()->where('created_at', '>=', $startDate)->count();
        $completed = table::creativeTasks()
            ->where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->count();
        
        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }

    private function getAverageCompletionTime($startDate)
    {
        try {
            $tasks = table::creativeTasks()
                ->where('status', 'completed')
                ->where('created_at', '>=', $startDate)
                ->whereNotNull('completed_at')
                ->get();

            if ($tasks->isEmpty()) return 0;

            $totalHours = $tasks->sum(function($task) {
                try {
                    return Carbon::parse($task->created_at)->diffInHours(Carbon::parse($task->completed_at));
                } catch (\Exception $e) {
                    return 0;
                }
            });

            return $tasks->count() > 0 ? round($totalHours / $tasks->count(), 1) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getEfficiencyScore($startDate)
    {
        $tasks = table::creativeTasks()
            ->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('estimated_minutes')
            ->whereNotNull('completed_at')
            ->get();

        if ($tasks->isEmpty()) return 0;

        $efficiencySum = $tasks->sum(function($task) {
            $estimated = $task->estimated_minutes;
            $actual = Carbon::parse($task->created_at)->diffInMinutes(Carbon::parse($task->completed_at));
            return $estimated > 0 ? min(($estimated / $actual) * 100, 200) : 100;
        });

        return round($efficiencySum / $tasks->count(), 1);
    }

    private function getWorkloadTrend($startDate)
    {
        return table::creativeTasks()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(function($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count
                ];
            });
    }

    private function getTopPerformers($startDate)
    {
        try {
            return DB::table('tbl_creative_task_assignments')
                ->join('tbl_creative_tasks', 'tbl_creative_task_assignments.task_id', '=', 'tbl_creative_tasks.id')
                ->join('tbl_people', 'tbl_creative_task_assignments.people_id', '=', 'tbl_people.id')
                ->where('tbl_creative_tasks.status', 'completed')
                ->where('tbl_creative_tasks.created_at', '>=', $startDate)
                ->select(
                    'tbl_people.firstname',
                    'tbl_people.lastname',
                    DB::raw('COUNT(*) as completed_tasks'),
                    DB::raw('COALESCE(AVG(TIMESTAMPDIFF(HOUR, tbl_creative_tasks.created_at, tbl_creative_tasks.completed_at)), 0) as avg_hours')
                )
                ->groupBy('tbl_people.id', 'tbl_people.firstname', 'tbl_people.lastname')
                ->orderBy('completed_tasks', 'desc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getBottlenecks($startDate)
    {
        return table::creativeTasks()
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, NOW())) as avg_age_hours'))
            ->where('created_at', '>=', $startDate)
            ->whereIn('status', ['pending', 'in_progress', 'review'])
            ->groupBy('status')
            ->orderBy('avg_age_hours', 'desc')
            ->get();
    }

    public function getPredictiveInsights()
    {
        return [
            'burnout_risk' => $this->getBurnoutRisk(),
            'capacity_forecast' => $this->getCapacityForecast(),
            'deadline_predictions' => $this->getDeadlinePredictions()
        ];
    }

    private function getBurnoutRisk()
    {
        try {
            return DB::table('tbl_creative_task_assignments')
                ->join('tbl_creative_tasks', 'tbl_creative_task_assignments.task_id', '=', 'tbl_creative_tasks.id')
                ->join('tbl_people', 'tbl_creative_task_assignments.people_id', '=', 'tbl_people.id')
                ->where('tbl_creative_tasks.created_at', '>=', now()->subDays(14))
                ->select(
                    'tbl_people.id',
                    'tbl_people.firstname',
                    'tbl_people.lastname',
                    DB::raw('COUNT(*) as task_count'),
                    DB::raw('SUM(CASE WHEN tbl_creative_tasks.status = "completed" THEN 1 ELSE 0 END) as completed'),
                    DB::raw('COALESCE(AVG(tbl_creative_tasks.estimated_minutes), 60) as avg_estimated_time')
                )
                ->groupBy('tbl_people.id', 'tbl_people.firstname', 'tbl_people.lastname')
                ->having('task_count', '>', 3)
                ->get()
                ->map(function($person) {
                    $completionRate = $person->task_count > 0 ? $person->completed / $person->task_count : 0;
                    $workload = $person->task_count * (($person->avg_estimated_time ?? 60) / 60);
                    $riskScore = (1 - $completionRate) * 50 + min($workload / 40, 1) * 50;
                    
                    return [
                        'name' => ($person->firstname ?? '') . ' ' . ($person->lastname ?? ''),
                        'risk_score' => round($riskScore, 1),
                        'task_count' => $person->task_count,
                        'completion_rate' => round($completionRate * 100, 1)
                    ];
                })
                ->sortByDesc('risk_score')
                ->take(5);
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getCapacityForecast()
    {
        $avgTasksPerDay = table::creativeTasks()
            ->where('created_at', '>=', now()->subDays(30))
            ->count() / 30;

        $activeVolunteers = DB::table('tbl_creative_task_assignments')
            ->join('tbl_creative_tasks', 'tbl_creative_task_assignments.task_id', '=', 'tbl_creative_tasks.id')
            ->where('tbl_creative_tasks.created_at', '>=', now()->subDays(30))
            ->distinct('people_id')
            ->count();

        $avgCompletionTime = $this->getAverageCompletionTime(now()->subDays(30));

        return [
            'daily_task_intake' => round($avgTasksPerDay, 1),
            'active_volunteers' => $activeVolunteers,
            'avg_completion_hours' => $avgCompletionTime,
            'capacity_utilization' => round(($avgTasksPerDay * $avgCompletionTime) / ($activeVolunteers * 8) * 100, 1)
        ];
    }

    private function getDeadlinePredictions()
    {
        return table::creativeTasks()
            ->whereIn('status', ['pending', 'in_progress', 'review'])
            ->whereNotNull('due_at')
            ->select('id', 'title', 'due_at', 'status', 'estimated_minutes')
            ->get()
            ->map(function($task) {
                $hoursRemaining = now()->diffInHours(Carbon::parse($task->due_at), false);
                $estimatedHours = ($task->estimated_minutes ?? 120) / 60;
                
                $riskLevel = 'low';
                if ($hoursRemaining < $estimatedHours) {
                    $riskLevel = 'high';
                } elseif ($hoursRemaining < $estimatedHours * 1.5) {
                    $riskLevel = 'medium';
                }
                
                return [
                    'task_id' => $task->id,
                    'title' => $task->title,
                    'due_at' => $task->due_at,
                    'hours_remaining' => round($hoursRemaining, 1),
                    'estimated_hours' => round($estimatedHours, 1),
                    'risk_level' => $riskLevel
                ];
            })
            ->sortBy('hours_remaining')
            ->take(10);
    }
}