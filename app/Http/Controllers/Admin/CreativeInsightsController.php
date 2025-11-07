<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class CreativeInsightsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            
            $metrics = $this->analyticsService->getPerformanceMetrics($days);
            $insights = $this->analyticsService->getPredictiveInsights();
            
            return view('admin.creative.insights.index', compact('metrics', 'insights', 'days'));
        } catch (\Exception $e) {
            return redirect()->route('admin.creative.index')
                ->with('error', 'Unable to load insights. Please ensure you have task data.');
        }
    }

    public function api(Request $request)
    {
        $days = $request->get('days', 30);
        $realtime = $request->get('realtime', false);
        
        if ($realtime) {
            return response()->json([
                'metrics' => $this->getRealtimeMetrics(),
                'activities' => $this->getRecentActivities(),
                'alerts' => $this->getActiveAlerts(),
                'chartData' => $this->getRealtimeChartData()
            ]);
        }
        
        return response()->json([
            'metrics' => $this->analyticsService->getPerformanceMetrics($days),
            'insights' => $this->analyticsService->getPredictiveInsights()
        ]);
    }

    public function realtime()
    {
        return view('admin.creative.insights.realtime');
    }

    private function getRealtimeMetrics()
    {
        try {
            return [
                'active_tasks' => \App\Classes\table::creativeTasks()->whereIn('status', ['pending', 'in_progress', 'review'])->count(),
                'completed_today' => \App\Classes\table::creativeTasks()->where('status', 'completed')->whereDate('updated_at', today())->count(),
                'overdue_tasks' => \App\Classes\table::creativeTasks()->where('due_at', '<', now())->whereNotIn('status', ['completed', 'cancelled'])->count(),
                'active_volunteers' => \DB::table('tbl_creative_task_assignments')->join('tbl_creative_tasks', 'tbl_creative_task_assignments.task_id', '=', 'tbl_creative_tasks.id')->where('tbl_creative_tasks.status', 'in_progress')->distinct('people_id')->count(),
                'avg_response_time' => round(\App\Classes\table::creativeTasks()->where('status', 'completed')->where('created_at', '>=', now()->subDays(7))->avg(\DB::raw('COALESCE(TIMESTAMPDIFF(HOUR, created_at, updated_at), 0)')) ?? 0, 1),
                'alert_count' => $this->getActiveAlerts()->count()
            ];
        } catch (\Exception $e) {
            return [
                'active_tasks' => 0,
                'completed_today' => 0,
                'overdue_tasks' => 0,
                'active_volunteers' => 0,
                'avg_response_time' => 0,
                'alert_count' => 0
            ];
        }
    }

    private function getRecentActivities()
    {
        return \App\Classes\table::creativeTasks()
            ->leftJoin('tbl_creative_task_assignments', 'tbl_creative_tasks.id', '=', 'tbl_creative_task_assignments.task_id')
            ->leftJoin('tbl_people', 'tbl_creative_task_assignments.people_id', '=', 'tbl_people.id')
            ->select('tbl_creative_tasks.*', 'tbl_people.firstname', 'tbl_people.lastname')
            ->where('tbl_creative_tasks.updated_at', '>=', now()->subHours(2))
            ->orderBy('tbl_creative_tasks.updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($task) {
                $type = $task->status === 'completed' ? 'completed' : ($task->status === 'in_progress' ? 'started' : 'assigned');
                return [
                    'type' => $type,
                    'title' => $task->title,
                    'description' => ($task->firstname ? $task->firstname . ' ' . $task->lastname : 'System') . ' • ' . ucwords(str_replace('_', ' ', $task->status)),
                    'time_ago' => \Carbon\Carbon::parse($task->updated_at)->diffForHumans()
                ];
            });
    }

    private function getActiveAlerts()
    {
        $alerts = collect();
        
        $overdue = \App\Classes\table::creativeTasks()->where('due_at', '<', now())->whereNotIn('status', ['completed', 'cancelled'])->count();
        if ($overdue > 0) {
            $alerts->push([
                'severity' => 'danger',
                'title' => 'Overdue Tasks',
                'message' => "{$overdue} tasks are past their due date"
            ]);
        }
        
        $highWorkload = \DB::table('tbl_creative_task_assignments')
            ->join('tbl_creative_tasks', 'tbl_creative_task_assignments.task_id', '=', 'tbl_creative_tasks.id')
            ->where('tbl_creative_tasks.status', 'in_progress')
            ->select('people_id', \DB::raw('COUNT(*) as task_count'))
            ->groupBy('people_id')
            ->having('task_count', '>', 5)
            ->count();
        
        if ($highWorkload > 0) {
            $alerts->push([
                'severity' => 'warning',
                'title' => 'High Workload',
                'message' => "{$highWorkload} volunteers have more than 5 active tasks"
            ]);
        }
        
        return $alerts;
    }

    private function getRealtimeChartData()
    {
        $hours = collect(range(0, 23))->map(function($hour) {
            return now()->startOfDay()->addHours($hour);
        });
        
        $created = $hours->map(function($hour) {
            return \App\Classes\table::creativeTasks()
                ->whereBetween('created_at', [$hour, $hour->copy()->addHour()])
                ->count();
        });
        
        $completed = $hours->map(function($hour) {
            return \App\Classes\table::creativeTasks()
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$hour, $hour->copy()->addHour()])
                ->count();
        });
        
        return [
            'labels' => $hours->map(fn($h) => $h->format('H:i'))->toArray(),
            'created' => $created->toArray(),
            'completed' => $completed->toArray()
        ];
    }
}