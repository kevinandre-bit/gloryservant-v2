<?php

namespace App\Http\Controllers\RadioDashboard\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index()
    {
        // Fake data (UI only)
        $kpi = [
            'monthTotal' => 2740.25,
            'dueToday'   => 3,
            'overdue'    => 2,
        ];

        $upcoming = [
            ['id'=>101,'date'=>'2025-09-03','vendor'=>'HostGator','category'=>'Hosting','amount'=>120.00,'site'=>'PAP','status'=>'Due','from_rule'=>'Monthly Hosting'],
            ['id'=>102,'date'=>'2025-09-05','vendor'=>'EDH','category'=>'Power','amount'=>260.00,'site'=>'Nord','status'=>'Scheduled','from_rule'=>'EDH – Nord'],
        ];

        $recent = [
            ['id'=>201,'date'=>'2025-08-27','vendor'=>'Digicel','category'=>'Link','amount'=>420.00,'site'=>'Sud-Est','status'=>'Paid'],
            ['id'=>202,'date'=>'2025-08-26','vendor'=>'Parts Depot','category'=>'Spare Parts','amount'=>199.99,'site'=>'Grand’Anse','status'=>'Paid'],
            ['id'=>203,'date'=>'2025-08-25','vendor'=>'Transport Co.','category'=>'Travel','amount'=>85.50,'site'=>'PAP','status'=>'Due'],
        ];

        $overdue = [
            ['id'=>301,'date'=>'2025-08-20','vendor'=>'EDH','category'=>'Power','amount'=>310.00,'site'=>'Nord-Ouest','status'=>'Overdue'],
            ['id'=>302,'date'=>'2025-08-18','vendor'=>'ISP','category'=>'Backhaul','amount'=>600.00,'site'=>'Centre','status'=>'Overdue'],
        ];

        return view('radio_dashboard.finance.expenses.index', compact('kpi','upcoming','recent','overdue'));
    }

    public function create()
    {
        $vendors   = ['EDH','Digicel','ISP','HostGator','Parts Depot','Transport Co.'];
        $categories= ['Power','Link','Hosting','Spare Parts','Travel','Licenses','Other'];
        $sites     = ['Port-au-Prince','Nord','Nord-Est','Nord-Ouest','Artibonite','Centre','Ouest','Sud-Est','Sud','Grand’Anse','Nippes'];
        return view('radio_dashboard.finance.expenses.create', compact('vendors','categories','sites'));
    }
}
