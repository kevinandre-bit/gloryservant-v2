<?php

namespace App\Http\Controllers\RadioDashboard\Finance;

use App\Http\Controllers\Controller;

class RecurringController extends Controller
{
    public function index()
    {
        // Fake rules (UI only)
        $rules = [
            ['id'=>1,'name'=>'Monthly Hosting','vendor'=>'HostGator','category'=>'Hosting','amount'=>120.00,'frequency'=>'Monthly','day'=>'1','site'=>'PAP','next_run'=>'2025-09-01','status'=>'Active'],
            ['id'=>2,'name'=>'EDH – Nord','vendor'=>'EDH','category'=>'Power','amount'=>260.00,'frequency'=>'Monthly','day'=>'5','site'=>'Nord','next_run'=>'2025-09-05','status'=>'Active'],
            ['id'=>3,'name'=>'Backhaul – Centre','vendor'=>'ISP','category'=>'Backhaul','amount'=>600.00,'frequency'=>'Monthly','day'=>'15','site'=>'Centre','next_run'=>'2025-09-15','status'=>'Paused'],
        ];

        return view('radio_dashboard.finance.recurring.index', compact('rules'));
    }

    public function create()
    {
        $vendors   = ['EDH','Digicel','ISP','HostGator','Parts Depot'];
        $categories= ['Power','Link','Hosting','Backhaul','Licenses','Other'];
        $sites     = ['Port-au-Prince','Nord','Nord-Est','Nord-Ouest','Artibonite','Centre','Ouest','Sud-Est','Sud','Grand’Anse','Nippes'];
        $frequencies = ['Weekly','Monthly','Quarterly','Yearly'];
        return view('radio_dashboard.finance.recurring.create', compact('vendors','categories','sites','frequencies'));
    }
}
