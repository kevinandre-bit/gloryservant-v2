<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\CreativeTaskEvent;
use App\Models\CreativePointsLedger;


class ComputeCreativeSnapshots extends Command
{
    protected $signature = 'creative:snapshots {--period=daily}';
    protected $description = 'Compute contribution snapshots for creative module (daily/weekly/monthly)';

    public function handle()
    {
        $period = $this->option('period') ?: 'daily';
        $this->info('Starting creative snapshots computation (period='.$period.')');

        // determine period start/end
        $now = Carbon::now();
        switch ($period) {
            case 'daily':
            case 'day':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $periodKey = 'day';
                break;
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $periodKey = 'month';
                break;
            case 'quarter':
                $start = $now->copy()->firstOfQuarter();
                $end = $now->copy()->lastOfQuarter();
                $periodKey = 'quarter';
                break;
            case 'year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $periodKey = 'year';
                break;
            case 'week':
            default:
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $periodKey = 'week';
                break;
        }

        $this->info("Computing snapshots for {$start->toDateString()} .. {$end->toDateString()}");

        // Aggregate tasks completed per person
        $tasks = CreativeTaskEvent::selectRaw('people_id, COUNT(*) as tasks_completed')
            ->where('event', 'completed')
            ->whereBetween('occurred_at', [$start, $end])
            ->groupBy('people_id')
            ->get()
            ->keyBy('people_id');

        // Aggregate minutes logged: join to tasks to sum estimated_minutes when completed
        $minutes = CreativeTaskEvent::selectRaw('tbl_creative_task_events.people_id, SUM(tbl_creative_tasks.estimated_minutes) as minutes_logged')
            ->join('tbl_creative_tasks', 'tbl_creative_task_events.task_id', '=', 'tbl_creative_tasks.id')
            ->where('tbl_creative_task_events.event', 'completed')
            ->whereBetween('tbl_creative_task_events.occurred_at', [$start, $end])
            ->groupBy('tbl_creative_task_events.people_id')
            ->get()
            ->keyBy('people_id');

        // Aggregate points earned per person
        $points = CreativePointsLedger::selectRaw('people_id, SUM(points) as points_earned')
            ->whereBetween('occurred_at', [$start, $end])
            ->groupBy('people_id')
            ->get()
            ->keyBy('people_id');

        // Union of people ids
        $peopleIds = collect(array_unique(array_merge($tasks->keys()->toArray(), $minutes->keys()->toArray(), $points->keys()->toArray())));

        foreach ($peopleIds as $peopleId) {
            $t = $tasks->get($peopleId)->tasks_completed ?? 0;
            $m = $minutes->get($peopleId)->minutes_logged ?? 0;
            $p = $points->get($peopleId)->points_earned ?? 0;

            DB::table('tbl_creative_contribution_snapshots')->updateOrInsert(
                [
                    'people_id' => $peopleId,
                    'period' => $periodKey,
                    'period_start' => $start->toDateString(),
                ],
                [
                    'period_end' => $end->toDateString(),
                    'tasks_completed' => $t,
                    'minutes_logged' => $m,
                    'points_earned' => $p,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->info('Completed snapshots for period='.$periodKey.'; processed '.count($peopleIds).' people.');
        return 0;
    }
}
