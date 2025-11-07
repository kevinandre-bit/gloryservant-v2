<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\table;
use Carbon\Carbon;

class AutoClockOut extends Command
{
    protected $signature = 'attendance:auto-clockout';
    protected $description = 'Auto clock-out anyone still logged in by 21:00, subtracting 4 hours';

    public function handle()
    {
        $today  = today()->toDateString();             // uses app timezone
        $cutoff = Carbon::parse("$today 21:00:00");

        // Grab rows dated yesterday where totalhours is NULL or empty
    $rows = table::attendance()
        ->where('date', $date)
        ->where(function($q) {
            $q->whereNull('totalhours')
              ->orWhere('totalhours', '');
        })
        ->get();

        foreach ($rows as $row) {
            $timeIn = Carbon::createFromFormat('Y-m-d h:i:s A', $row->timein);

            $rawHrs = max(0, $timeIn->diffInMinutes($cutoff) / 60.0 - 4);
            $hours  = floor($rawHrs);
            $mins   = floor(($rawHrs - $hours) * 60);
            $total  = "{$hours}.{$mins}";

            table::attendance()
                 ->where('id', $row->id)
                 ->update([
                     'timeout'        => $cutoff->format('Y-m-d h:i:s A'),
                     'totalhours'     => $total,
                     'status_timeout' => 'Auto Clock-Out',
                 ]);
        }

        $this->info("Auto clock-out completed for ".count($rows)." record(s).");
    }
}