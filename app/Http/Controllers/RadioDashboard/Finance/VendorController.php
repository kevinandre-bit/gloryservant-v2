<?php

namespace App\Http\Controllers\RadioDashboard\Finance;

use App\Http\Controllers\Controller;

class VendorController extends Controller
{
    public function index()
    {
        // Fake data for UI-only listing
        $vendors = [
            [
                'name'     => 'EDH',
                'type'     => 'Utility',
                'contact'  => 'edh@example.com',
                'phone'    => '(509) 555-0101',
                'country'  => 'Haiti',
                'city'     => 'Port-au-Prince',
                'pay'      => 'Bank Transfer',
            ],
            [
                'name'     => 'Digicel',
                'type'     => 'Telco',
                'contact'  => 'ops@digicel.ht',
                'phone'    => '(509) 555-0102',
                'country'  => 'Haiti',
                'city'     => 'Port-au-Prince',
                'pay'      => 'Mobile Money',
            ],
            [
                'name'     => 'ISP',
                'type'     => 'Backhaul',
                'contact'  => 'noc@isp.ht',
                'phone'    => '(509) 555-0103',
                'country'  => 'Haiti',
                'city'     => 'Cap-Haïtien',
                'pay'      => 'Bank Transfer',
            ],
            [
                'name'     => 'HostGator',
                'type'     => 'Hosting',
                'contact'  => 'support@hostgator.com',
                'phone'    => '+1 800 000 0000',
                'country'  => 'United States',
                'city'     => 'Houston',
                'pay'      => 'Card',
            ],
        ];

        return view('radio_dashboard.finance.vendors.index', compact('vendors'));
    }

    public function create()
    {
        // Dropdown data for the form
        $types          = ['Utility','Telco','Hosting','Backhaul','Retail','Other'];
        $paymentMethods = ['Bank Transfer','Mobile Money','Cash','Check','Card'];
        $countries      = ['Haiti','United States','Canada','Dominican Republic','France','Other'];
        $currencies     = ['HTG','USD','CAD','EUR','DOP'];

        return view('radio_dashboard.finance.vendors.create', compact(
            'types','paymentMethods','countries','currencies'
        ));
    }
}