<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register Artisan commands.
     */
    protected $commands = [
        \App\Console\Commands\RadioNormalizeTracks::class,
        \App\Console\Commands\RadioClearLibrary::class,
        \App\Console\Commands\ComputeCreativeSnapshots::class,
        \App\Console\Commands\CalculateAttendancePoints::class,
        // add others here…
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
{
    // Examples
    // $schedule->command('inspire')->hourly();

    // 1. Deactivate expired schedules daily at 3:39 PM
    $schedule->command('schedules:deactivate-expired')
        ->dailyAt('15:39');

    // 2. Generate daily QR codes at midnight
    $schedule->command('qr:generate-daily')
        ->dailyAt('00:00');

    // 3. Automatically clock out users daily at 9:00 PM
    // "withoutOverlapping()" prevents the command from running again if the previous run is still in progress.
    $schedule->command('attendance:auto-clockout')
        ->dailyAt('21:00')
        ->withoutOverlapping();

    // 4. Trigger Zoho integration every 15 minutes
    $schedule->command('zoho:trigger-flow')
        ->everyFifteenMinutes();

    // 5. Warm up dashboard cache every 5 minutes
    $schedule->command('dashboard:warm')
        ->everyFiveMinutes();

    // 6. Import radio library daily at 2:00 AM, only if the CSV file exists
    $schedule->command('radio:import-library')
        ->dailyAt('02:00')
        ->when(fn () => file_exists(storage_path('app/library_export.csv')));

    // 7. Compute creative contribution snapshots daily at 03:30
    $schedule->command('creative:snapshots --period=daily')
        ->dailyAt('03:30')
        ->withoutOverlapping();

    // 8. Calculate attendance points daily at 1:00 AM
    $schedule->command('creative:attendance-points')
        ->dailyAt('01:00')
        ->withoutOverlapping();
        
    // 9. Check and award badges daily at 2:00 AM
    $schedule->call(function () {
        $badgeService = new \App\Services\BadgeService();
        $peopleIds = \DB::table('tbl_people')->pluck('id');
        foreach ($peopleIds as $peopleId) {
            $badgeService->checkAndAwardBadges($peopleId);
        }
    })->dailyAt('02:00');
}


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }

}