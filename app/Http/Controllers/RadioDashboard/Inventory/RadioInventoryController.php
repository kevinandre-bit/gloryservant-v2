<?php

namespace App\Http\Controllers\RadioDashboard\Inventory;

use App\Http\Controllers\Controller;

class RadioInventoryController extends Controller
{
    public function index()
    {
        // Fake inventory items
        $items = [
            [
                'sku' => 'RF-COAX-7/8',
                'name' => '7/8" Coax Cable (per 50m)',
                'category' => 'RF',
                'site' => 'Port-au-Prince',
                'qty' => 4,
                'uom' => 'roll',
                'min' => 2,
                'vendor' => 'SignalParts Inc.',
                'status' => 'OK',
            ],
            [
                'sku' => 'UPS-BATT-9AH',
                'name' => 'UPS Battery 12V 9Ah',
                'category' => 'Power',
                'site' => 'Les Cayes',
                'qty' => 3,
                'uom' => 'unit',
                'min' => 5,
                'vendor' => 'ElectroPro',
                'status' => 'LOW',
            ],
            [
                'sku' => 'FAN-120MM',
                'name' => '120mm Cooling Fan',
                'category' => 'Cooling',
                'site' => 'Jérémie',
                'qty' => 0,
                'uom' => 'unit',
                'min' => 2,
                'vendor' => 'CoolAir',
                'status' => 'OUT',
            ],
        ];

        return view('radio_dashboard.inventory.index', compact('items'));
    }

    public function movements()
    {
        // Fake stock movements (last entries)
        $movements = [
            ['when' => '2025-08-22 14:10', 'type' => 'RECEIVE', 'sku' => 'UPS-BATT-9AH', 'name'=>'UPS Battery 12V 9Ah', 'qty'=>10, 'to'=>'PAP', 'by'=>'Admin'],
            ['when' => '2025-08-22 10:05', 'type' => 'ISSUE',   'sku' => 'FAN-120MM',    'name'=>'120mm Cooling Fan', 'qty'=>2,  'to'=>'Les Cayes', 'by'=>'Jean M.'],
            ['when' => '2025-08-21 16:40', 'type' => 'TRANSFER','sku'=>'RF-COAX-7/8',    'name'=>'7/8" Coax Cable',   'qty'=>1,  'from'=>'PAP','to'=>'Jérémie','by'=>'Admin'],
        ];

        // Quick selects for forms
        $sites = ['Port-au-Prince','Les Cayes','Jérémie','Hinche','Cap-Haïtien'];
        $items = [
            ['sku'=>'RF-COAX-7/8','name'=>'7/8" Coax Cable'],
            ['sku'=>'UPS-BATT-9AH','name'=>'UPS Battery 12V 9Ah'],
            ['sku'=>'FAN-120MM','name'=>'120mm Cooling Fan'],
        ];

        return view('radio_dashboard.inventory.movements', compact('movements','sites','items'));
    }

    public function create()
    {
        // Fake dropdowns
        $categories = ['RF','Power','Cooling','Cabling','Tools'];
        $sites      = ['Port-au-Prince','Les Cayes','Jérémie','Hinche','Cap-Haïtien'];
        $vendors    = ['SignalParts Inc.','ElectroPro','CoolAir'];

        return view('radio_dashboard.inventory.items_create', compact('categories','sites','vendors'));
    }
}