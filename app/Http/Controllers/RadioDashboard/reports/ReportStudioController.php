<?php
// app/Http/Controllers/RadioDashboard/Reports/ReportStudioController.php

namespace App\Http\Controllers\RadioDashboard\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportStudioController extends Controller
{
    public function studio(Request $request)
    {
        $kpis = ['matchPct'=>93, 'uptimePct'=>99.1, 'critAlerts'=>2];
        $recent = [
            ['title'=>'Daily Operations — 2025-08-22', 'type'=>'daily_admin', 'when'=>'2h ago'],
            ['title'=>'Weekly Summary — W34',          'type'=>'weekly',      'when'=>'2d ago'],
        ];
        return view('radio_dashboard.reports.admin.studio', compact('kpis','recent'));
    }

    // app/Http/Controllers/RadioDashboard/Reports/ReportStudioController.php

public function build(Request $request)
{
    $type = $request->get('type', 'daily_admin');

    $title = 'Custom Report';
    switch ($type) {
        case 'daily_tech':  $title = 'Daily Technician Report';  break;
        case 'daily_op':    $title = 'Daily Operator Report';    break;
        case 'daily_admin': $title = 'Daily Operations Report';  break;
        case 'weekly':      $title = 'Weekly Broadcast Summary'; break;
    }

    // Single-day builder (admin daily)
    $date = $request->get('from', \Illuminate\Support\Carbon::now()->format('Y-m-d'));

    // Fake data for the interface (no DB yet)
    $adminDefaults = [
        'prepared_by' => 'Admin – Jane Doe',
        'date'        => $date,

        // Technicians schedule today
        'tech' => [
            ['name'=>'Technician A','shift'=>'08:00 – 16:00','present'=>true,  'notes'=>'Handled Les Cayes incident'],
            ['name'=>'Technician B','shift'=>'08:00 – 16:00','present'=>true,  'notes'=>'Managed Cap-Haïtien storm issue'],
            ['name'=>'Technician C','shift'=>'—',            'present'=>false, 'notes'=>'Off duty'],
        ],

        // Incident queue (as if submitted by technicians)
        'incidents' => [
            ['id'=>101,'station'=>'Les Cayes','time'=>'14:10–16:00','issue'=>'Transmitter overheated','downtime'=>'1h 50m','resolution'=>'Fan replaced, TX restarted','status'=>'open'],
            ['id'=>102,'station'=>'Cap-Haïtien','time'=>'09:30–10:00','issue'=>'Signal loss (storm)','downtime'=>'30m','resolution'=>'Antenna realigned','status'=>'open'],
        ],

        // Overall status
        'overall' => [
            'stations_monitored' => 10,
            'uptime_pct'         => 98.9,
            'total_downtime'     => '2h 20m (Les Cayes, Cap-Haïtien)',
            'remark'             => '',
        ],

        // Text areas
        'observations'    => "Coverage was sufficient (2 techs on duty).\nIncidents resolved quickly.",
        'todo'            => [
            ['text'=>'Buy spare cooling fans for Les Cayes','done'=>false],
            ['text'=>'Plan antenna reinforcement for Cap-Haïtien','done'=>false],
            ['text'=>'Review storm SOP for coastal stations','done'=>false],
        ],
    ];

    // Weekly / other builder keeps a simple notice (we’re focusing on daily_admin UI)
    return view('radio_dashboard.reports.admin.build', [
        'type'          => $type,
        'title'         => $title,
        'date'          => $date,
        'adminDefaults' => $adminDefaults,
    ]);
}
    public function preview(Request $request, string $type)
    {
        $title = 'Custom Report';
        switch ($type) {
            case 'daily_tech':  $title = 'Daily Technician Report';  break;
            case 'daily_op':    $title = 'Daily Operator Report';    break;
            case 'daily_admin': $title = 'Daily Operations Report';  break;
            case 'weekly':      $title = 'Weekly Broadcast Summary'; break;
        }

        $meta = [
            'title'  => $title,
            'type'   => $type,
            'period' => [
                'from' => $request->get('from', now()->toDateString()),
                'to'   => $request->get('to',   $request->get('from', now()->toDateString())),
            ],
            'prepared_by' => $request->get('prepared_by', 'Admin – Jane Doe'),
        ];

        // If Admin Daily Summary, compile exactly your sections from the form (with safe fallbacks)
        if ($type === 'daily_admin') {
            $admin = [
                'date' => $meta['period']['from'],

                'tech' => array_values($request->get('tech', [
                    ['name'=>'Technician A','shift'=>'08:00 – 16:00','status'=>'Present','notes'=>'Handled Les Cayes incident'],
                    ['name'=>'Technician B','shift'=>'08:00 – 16:00','status'=>'Present','notes'=>'Managed Cap-Haïtien storm issue'],
                    ['name'=>'Technician C','shift'=>'–','status'=>'Off duty','notes'=>'N/A'],
                ])),

                'overall' => [
                    'stations_monitored' => (int)$request->get('stations_monitored', 10),
                    'uptime_pct'         => (float)$request->get('uptime_pct', 98.9),
                    'total_downtime'     => $request->get('total_downtime', '2h 20m (Les Cayes, Cap-Haïtien)'),
                ],

                'incidents' => array_values($request->get('incidents', [
                    ['station'=>'Les Cayes','time'=>'14:10–16:00','issue'=>'Transmitter overheated','downtime'=>'1h 50m','resolution'=>'Fan replaced, transmitter restarted','followup'=>'Order new spare fans (2nd failure in 2 months)'],
                    ['station'=>'Cap-Haïtien','time'=>'09:30–10:00','issue'=>'Brief signal loss (storm)','downtime'=>'30m','resolution'=>'Antenna realigned','followup'=>'Monitor stability during next storm'],
                ])),

                'performance' => array_values($request->get('performance', [
                    ['tech'=>'Technician A','tasks'=>'Resolved Les Cayes overheating','notes'=>'Reliable, proactive'],
                    ['tech'=>'Technician B','tasks'=>'Fixed Cap-Haïtien signal issue','notes'=>'Responded quickly'],
                    ['tech'=>'Technician C','tasks'=>'Off duty','notes'=>'–'],
                ])),

                'observations'   => $request->get('observations',
                    "Schedule coverage was sufficient (2 techs on duty).\nBoth incidents resolved quickly.\nPreventive maintenance recommendation needed for Les Cayes & Cap-Haïtien."
                ),
                'manager_actions'=> $request->get('manager_actions',
                    "Approve purchase of spare cooling fans for Les Cayes.\nConsider antenna reinforcement plan for Cap-Haïtien.\nReview storm preparedness procedures for all coastal stations."
                ),
            ];

            return view('radio_dashboard.reports.admin.preview', compact('meta','admin'));
        }

        // Fallback to your earlier generic preview for other types (kept minimal)
        $programming = ['matchPct'=>93,'deviations'=>[],'adsTop5'=>[]];
        $technical   = ['networkUptime'=>99.2,'incidents'=>[],'sites7d'=>[]];
        $audience    = ['yt_avg'=>820,'app_avg'=>540,'peak'=>'19:05','contrib'=>['count'=>47,'amount'=>1250]];
        $compliance  = ['hosting'=>['status'=>'Up to date','renewal'=>'2026-01-31'],'licenses'=>[]];
        $budget      = ['requests'=>[]];

        return view('radio_dashboard.reports.admin.preview', compact(
            'meta','programming','technical','audience','compliance','budget'
        ));
    }
}