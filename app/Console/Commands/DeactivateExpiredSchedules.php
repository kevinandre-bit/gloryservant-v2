<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeactivateExpiredSchedules extends Command
{
    protected $signature = 'schedules:deactivate-expired';
    protected $description = 'Deactivate schedules whose dateto is before today';

    public function handle()
    {
        $affected = DB::table('tbl_people_schedules')
            ->where('dateto', '<', now()->toDateString())
            ->where('is_active', 1)
            ->update(['is_active' => 0]);

        $this->info("Deactivated {$affected} expired schedules.");
    }
}