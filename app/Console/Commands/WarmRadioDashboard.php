<?php
// app/Console/Commands/WarmRadioDashboard.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RadioDashboardSnapshot;

class WarmRadioDashboard extends Command
{
    protected $signature = 'dashboard:warm';
    protected $description = 'Precompute and cache radio dashboard KPIs';

    public function handle()
    {
        RadioDashboardSnapshot::warm();
        $this->info('Dashboard cache warmed.');
        return Command::SUCCESS;
    }
}