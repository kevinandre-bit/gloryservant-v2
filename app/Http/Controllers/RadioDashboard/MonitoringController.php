<?php

namespace App\Http\Controllers\RadioDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    // ===== SOURCE (MIAMI) =====
    public function source()
    {
        // Fake encoder health
        $now = Carbon::now();
        $series24h = [];
        for ($i = 23; $i >= 0; $i--) {
            // uptime percent snapshots per hour
            $series24h[] = 98 + rand(0, 20) / 10; // 98.0% - 100.0%
        }

        $disconnects = [
            ['started_at' => $now->copy()->subHours(7)->subMinutes(12)->format('Y-m-d H:i'),  'duration' => '02m 10s'],
            ['started_at' => $now->copy()->subDay()->subMinutes(45)->format('Y-m-d H:i'),     'duration' => '01m 05s'],
            ['started_at' => $now->copy()->subDays(2)->subMinutes(3)->format('Y-m-d H:i'),    'duration' => '03m 40s'],
        ];

        $data = [
            'encoder' => [
                'status'   => 'ONLINE',
                'bitrate'  => 192,        // kbps
                'codec'    => 'AAC-LC',
                'uplink'   => 'SRT',
                'latency'  => 120,        // ms
                'uptime24' => 99.4,
            ],
            'series24h'    => $series24h,
            'disconnects'  => $disconnects,
            'last_drop'    => $disconnects[0] ?? null,
            'lastUpdatedAt'=> $now->format('Y-m-d H:i'),
        ];

        return view('radio_dashboard.monitoring.source', compact('data'));
    }

    // ===== HUB (PAP) =====
    public function hub()
    {
        $sites = [
            ['id'=>1,'name'=>'Nord'],
            ['id'=>2,'name'=>'Nord-Est'],
            ['id'=>3,'name'=>'Nord-Ouest'],
            ['id'=>4,'name'=>'Artibonite'],
            ['id'=>5,'name'=>'Centre'],
            ['id'=>6,'name'=>'Ouest'],
            ['id'=>7,'name'=>'Sud-Est'],
            ['id'=>8,'name'=>'Sud'],
            ['id'=>9,'name'=>"Grand'Anse"],
            ['id'=>10,'name'=>'Nippes'],
        ];

        // Fake link status (OK/ISSUE) & parity %
        $links = [];
        $okCount = 0;
        foreach ($sites as $s) {
            $ok = rand(0, 100) > 8; // ~92% OK
            $okCount += $ok ? 1 : 0;
            $links[] = [
                'site'       => $s['name'],
                'status'     => $ok ? 'OK' : 'ISSUE',
                'latency_ms' => rand(40, 180),
                'jitter_ms'  => rand(1, 20),
                'loss_pct'   => rand(0, 15) / 10,
                'parity_pct' => rand(92, 100),
                'history'    => [
                    ['t' => now()->subHours(3)->format('H:i'), 'state' => 'OK'],
                    ['t' => now()->subHours(2)->format('H:i'), 'state' => $ok ? 'OK' : 'ISSUE'],
                ],
            ];
        }

        $data = [
            'ingest' => [
                'from'         => 'SOURCE (Miami)',
                'status'       => 'LOCKED',
                'drift_ms'     => rand(0, 30),
                'uptime24_pct' => 99.3,
            ],
            'links' => $links,
            'linksOk' => $okCount,
            'linksTotal' => count($links),
            'parityYesterday' => [
                'expected_blocks' => 1440,
                'matched_blocks'  => 1406,
            ],
            'routes' => [
                'reprobe' => route('monitoring.hub'),
                'site'    => fn($id) => route('monitoring.sites.show', $id),
            ],
        ];

        return view('radio_dashboard.monitoring.hub', compact('data', 'sites'));
    }

    // ===== TX SITES LIST =====
    public function sitesIndex()
{
    $sites = [
        ['id'=>1,'department'=>'Ouest','capital'=>'Port-au-Prince','frequency'=>'103.3','freq_status'=>'Acquired','online'=>true,'rf'=>81,'snr'=>27,'uptime'=>99.5,'power'=>'Stable','last_maint'=>'2025-08-21'],
        ['id'=>2,'department'=>'Sud','capital'=>'Aux Cayes','frequency'=>'104.7','freq_status'=>'Acquired','online'=>true,'rf'=>72,'snr'=>23,'uptime'=>97.1,'power'=>'Stable','last_maint'=>'2025-08-15'],
        ['id'=>3,'department'=>'Sud-Est','capital'=>'Jacmel','frequency'=>'95.5','freq_status'=>'Acquired','online'=>true,'rf'=>69,'snr'=>22,'uptime'=>96.7,'power'=>'Flaky','last_maint'=>'2025-08-09'],
        ['id'=>4,'department'=>"Grand'Anse",'capital'=>'Jeremie','frequency'=>'93.7','freq_status'=>'Acquired','online'=>true,'rf'=>68,'snr'=>21,'uptime'=>95.9,'power'=>'Outage','last_maint'=>'2025-08-05'],
        ['id'=>5,'department'=>'Centre','capital'=>'Hinche','frequency'=>'90.3','freq_status'=>'Acquired','online'=>true,'rf'=>76,'snr'=>25,'uptime'=>98.9,'power'=>'Stable','last_maint'=>'2025-08-18'],
        ['id'=>6,'department'=>'Nord-Ouest','capital'=>'Port-de-Paix','frequency'=>'93.7','freq_status'=>'Acquired','online'=>false,'rf'=>0,'snr'=>0,'uptime'=>96.9,'power'=>'Outage','last_maint'=>'2025-08-10'],
        ['id'=>7,'department'=>'Nippes','capital'=>'Miragoâne','frequency'=>'91.9','freq_status'=>'Acquired','online'=>true,'rf'=>71,'snr'=>22,'uptime'=>96.2,'power'=>'Stable','last_maint'=>'2025-08-14'],
        ['id'=>8,'department'=>'Nord-Est','capital'=>'Fort Liberté','frequency'=>'—','freq_status'=>'Not Acquired','online'=>true,'rf'=>74,'snr'=>24,'uptime'=>98.8,'power'=>'Stable','last_maint'=>'2025-08-19'],
    ];

    return view('radio_dashboard.monitoring.sites.index', compact('sites'));
}
    // ===== TX SITE DETAIL =====
    public function sitesShow($site)
    {
        // Normally fetch by id/slug. For demo, map id→name.
        $names = [1=>'Nord',2=>'Nord-Est',3=>'Nord-Ouest',4=>'Artibonite',5=>'Centre',6=>'Ouest',7=>'Sud-Est',8=>'Sud',9=>"Grand'Anse",10=>'Nippes'];
        $name = $names[(int)$site] ?? 'Unknown';

        // 7-day uptime up/down minutes (fake)
        $days = [];
        for ($i=6; $i>=0; $i--) {
            $up   = rand(1350, 1440);                // minutes up
            $down = 1440 - $up;
            $days[] = [
                'date' => now()->subDays($i)->toDateString(),
                'up'   => $up,
                'down' => $down
            ];
        }

        $snrTrend = [];
        for ($i=1; $i<=24; $i++) $snrTrend[] = rand(19, 28);

        $incidents = [
            ['id'=>2103,'title'=>'Link flap detected','severity'=>'warn','open_at'=>now()->subHours(4)->format('Y-m-d H:i'),'status'=>'open'],
            ['id'=>2078,'title'=>'Power outage (grid)','severity'=>'crit','open_at'=>now()->subDays(1)->format('Y-m-d H:i'),'status'=>'closed'],
        ];

        $maintenance = [
            ['id'=>501,'title'=>'Replace coax connector','type'=>'Corrective','status'=>'open','due_date'=>now()->addDays(2)->toDateString()],
            ['id'=>498,'title'=>'Quarterly PM check','type'=>'Preventive','status'=>'done','due_date'=>now()->subDays(10)->toDateString()],
        ];

        $compliance = [
            ['kind'=>'License','name'=>'ANATEL-RF','due'=>'2025-10-15','status'=>'active'],
            ['kind'=>'Hosting','name'=>'Tower Lease','due'=>'2025-09-30','status'=>'due-soon'],
            ['kind'=>'Tax','name'=>'Local Broadcast Tax','due'=>'2025-12-31','status'=>'active'],
        ];

        $data = [
            'id'        => (int)$site,
            'name'      => $name,
            'rf_strength'=> rand(65, 82),
            'snr_avg'   => array_sum($snrTrend)/count($snrTrend),
            'uptime7'   => $days,
            'snrTrend'  => $snrTrend,
            'power'     => ['status'=>['Stable','Flaky','Outage'][rand(0,2)], 'last_event'=>now()->subHours(rand(1,24))->format('Y-m-d H:i')],
            'incidents' => $incidents,
            'maintenance'=>$maintenance,
            'compliance'=> $compliance,
        ];

        return view('radio_dashboard.monitoring.sites.show', compact('data'));
    }
}