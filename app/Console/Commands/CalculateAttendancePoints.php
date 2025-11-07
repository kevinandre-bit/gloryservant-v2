<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PointsService;
use DB;

class CalculateAttendancePoints extends Command
{
    protected $signature = 'creative:attendance-points {--date=}';
    protected $description = 'Calculate and award points based on attendance hours';

    public function handle()
    {
        $date = $this->option('date') ? $this->option('date') : now()->format('Y-m-d');
        
        $this->info("Calculating attendance points for {$date}...");
        
        $attendanceRecords = DB::table('tbl_people_attendance')
            ->where('date', $date)
            ->whereNotNull('totalhours')
            ->where('totalhours', '!=', '')
            ->get();

        $pointsService = new PointsService();
        $totalAwarded = 0;

        foreach ($attendanceRecords as $record) {
            $minutes = $this->parseTimeToMinutes($record->totalhours);
            if ($minutes > 0) {
                $points = $pointsService->awardAttendancePoints($record->reference, $minutes, $date);
                $totalAwarded += $points;
            }
        }

        $this->info("Awarded {$totalAwarded} points to " . count($attendanceRecords) . " people");
    }

    private function parseTimeToMinutes($timeString)
    {
        if (preg_match('/(\d+):(\d+)/', $timeString, $matches)) {
            return ($matches[1] * 60) + $matches[2];
        }
        return 0;
    }
}