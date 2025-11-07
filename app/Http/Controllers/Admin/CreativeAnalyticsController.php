<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Classes\table;
use DB;

class CreativeAnalyticsController extends Controller
{
    public function index()
    {
        $stats = [
            'total_requests' => table::creativeRequests()->count(),
            'pending_requests' => table::creativeRequests()->where('status', 'pending')->count(),
            'active_tasks' => table::creativeTasks()->whereIn('status', ['in_progress', 'review'])->count(),
            'completed_today' => table::creativeTasks()->where('status', 'completed')
                ->whereDate('updated_at', today())->count(),
        ];

        $requestsByType = table::creativeRequests()
            ->select('request_type', DB::raw('count(*) as count'))
            ->groupBy('request_type')
            ->get();

        $tasksByStatus = table::creativeTasks()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $leaderboard = DB::table('tbl_creative_points_ledger')
            ->join('tbl_people', 'tbl_creative_points_ledger.people_id', '=', 'tbl_people.id')
            ->select('tbl_people.id', 'tbl_people.firstname', 'tbl_people.lastname', 
                DB::raw('SUM(points) as total_points'))
            ->groupBy('tbl_people.id', 'tbl_people.firstname', 'tbl_people.lastname')
            ->orderBy('total_points', 'desc')
            ->limit(10)
            ->get();

        // Workload over time (last 30 days)
        $workloadData = table::creativeTasks()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return view('admin.creative.analytics.index', compact('stats', 'requestsByType', 'tasksByStatus', 'leaderboard', 'workloadData'));
    }
}
