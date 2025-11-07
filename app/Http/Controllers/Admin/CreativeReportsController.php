<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class CreativeReportsController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        return view('admin.creative.reports.index');
    }

    public function exportTasks(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        $tasks = $this->reportService->generateTaskReport($startDate, $endDate);
        
        return $this->reportService->exportTasksCSV($tasks);
    }

    public function exportContributions(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        $contributions = $this->reportService->generateContributionReport($startDate, $endDate);
        
        return $this->reportService->exportContributionsCSV($contributions);
    }
}