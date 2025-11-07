<?php

namespace App\Http\Controllers\RadioDashboard\Tech;

use App\Http\Controllers\Controller;

class TechReportsController extends Controller
{
    public function create()
    {
        // Fake sites (no DB)
        $sites = [
            ['id'=>1, 'name'=>'Ouest — Port-au-Prince', 'frequency'=>'103.3', 'status'=>'Acquired'],
            ['id'=>2, 'name'=>'Sud — Les Cayes', 'frequency'=>'104.7', 'status'=>'Acquired'],
            ['id'=>3, 'name'=>'Sud-Est — Jacmel', 'frequency'=>'95.5', 'status'=>'Acquired'],
            ['id'=>4, 'name'=>"Grand'Anse — Jérémie", 'frequency'=>'93.7', 'status'=>'Acquired'],
            ['id'=>5, 'name'=>'Centre — Hinche', 'frequency'=>'90.3', 'status'=>'Acquired'],
            ['id'=>6, 'name'=>'Nord-Ouest — Port-de-Paix', 'frequency'=>'93.7', 'status'=>'Acquired'],
            ['id'=>7, 'name'=>'Nippes — Miragoâne', 'frequency'=>'91.9', 'status'=>'Acquired'],
            ['id'=>8, 'name'=>'Nord-Est — Fort-Liberté', 'frequency'=>'—', 'status'=>'Not acquired'],
        ];

        // Simulate which ones the tech is assigned to today (first 3)
        $assignedSiteIds = [1,2,3];

        // “Maintenance scheduled today” flag (fake)
        $scheduledToday = true;

        // Fake defaults for quick demo fill
        $defaults = [
            'date'            => now()->toDateString(),
            'signal_status'   => 'Good',
            'uptime_hours'    => 22,
            'downtime_hours'  => 2,
            'transmitter'     => 'OK',
            'antenna'         => 'OK',
            'ups'             => 'OK',
        ];

        return view('radio_dashboard.tech.reports.daily.create', compact(
            'sites', 'assignedSiteIds', 'scheduledToday', 'defaults'
        ));
    }

    // Optional: a fake "preview" endpoint to show submitted values without DB
    public function preview()
    {
        // Just echo back what was posted (never store)
        $data = request()->all();
        return response()->view('radio_dashboard.tech.reports.daily.preview', ['data' => $data]);
    }
}
