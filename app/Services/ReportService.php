<?php

namespace App\Services;

use App\Classes\table;
use DB;
use Carbon\Carbon;

class ReportService
{
    public function generateTaskReport($startDate = null, $endDate = null)
    {
        $query = table::creativeTasks()
            ->join('tbl_creative_requests', 'tbl_creative_tasks.request_id', '=', 'tbl_creative_requests.id')
            ->leftJoin('tbl_creative_task_assignments', 'tbl_creative_tasks.id', '=', 'tbl_creative_task_assignments.task_id')
            ->leftJoin('tbl_people', 'tbl_creative_task_assignments.people_id', '=', 'tbl_people.id')
            ->select(
                'tbl_creative_tasks.*',
                'tbl_creative_requests.title as request_title',
                'tbl_creative_requests.request_type',
                'tbl_people.firstname',
                'tbl_people.lastname'
            );

        if ($startDate) {
            $query->where('tbl_creative_tasks.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('tbl_creative_tasks.created_at', '<=', $endDate);
        }

        return $query->orderBy('tbl_creative_tasks.created_at', 'desc')->get();
    }

    public function generateContributionReport($startDate = null, $endDate = null)
    {
        $query = DB::table('tbl_creative_points_ledger')
            ->join('tbl_people', 'tbl_creative_points_ledger.people_id', '=', 'tbl_people.id')
            ->select(
                'tbl_people.firstname',
                'tbl_people.lastname',
                'tbl_creative_points_ledger.points',
                'tbl_creative_points_ledger.reason',
                'tbl_creative_points_ledger.created_at'
            );

        if ($startDate) {
            $query->where('tbl_creative_points_ledger.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('tbl_creative_points_ledger.created_at', '<=', $endDate);
        }

        return $query->orderBy('tbl_creative_points_ledger.created_at', 'desc')->get();
    }

    public function exportTasksCSV($data)
    {
        $filename = 'creative_tasks_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Task Title', 'Request Title', 'Request Type', 'Status', 'Assignee', 'Created At', 'Updated At']);
            
            foreach ($data as $task) {
                fputcsv($file, [
                    $task->title,
                    $task->request_title,
                    $task->request_type,
                    $task->status,
                    $task->firstname ? $task->firstname . ' ' . $task->lastname : 'Unassigned',
                    $task->created_at,
                    $task->updated_at
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportContributionsCSV($data)
    {
        $filename = 'creative_contributions_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Volunteer', 'Points', 'Reason', 'Date']);
            
            foreach ($data as $contribution) {
                fputcsv($file, [
                    $contribution->firstname . ' ' . $contribution->lastname,
                    $contribution->points,
                    $contribution->reason,
                    $contribution->created_at
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}