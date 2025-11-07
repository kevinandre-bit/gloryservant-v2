<?php

namespace App\Http\Controllers\RadioDashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poc;
use App\Models\Station;

class PocRegistryController extends Controller
{
    /** List all POCs */
    public function index()
    {
        $pocs = Poc::with('station')->latest()->get();
        return view('radio_dashboard.admin.pocs_index', compact('pocs'));
    }

    /** Show create form (uses your existing view name) */
    public function create()
    {
        $stations = Station::orderBy('name')->get(['id','name','frequency','department']);
        return view('radio_dashboard.admin.pocs_create', compact('stations'));
    }

    /** Persist new POC */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:120',
            'role'       => 'nullable|string|max:120',
            'phone'      => 'nullable|string|max:60',
            'email'      => 'nullable|email|max:120',
            'station_id' => 'nullable|integer|exists:stations,id',
        ]);

        Poc::create($data);
        return redirect()->route('radio.admin.pocs.index')->with('ok', 'POC saved.');
    }

    /** Show edit form */
    public function edit(Poc $poc)
    {
        $stations = Station::orderBy('name')->get(['id','name','frequency','department']);
        return view('radio_dashboard.admin.pocs_edit', compact('poc','stations'));
    }

    /** Update POC */
    public function update(Request $request, Poc $poc)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:120',
            'role'       => 'nullable|string|max:120',
            'phone'      => 'nullable|string|max:60',
            'email'      => 'nullable|email|max:120',
            'station_id' => 'nullable|integer|exists:stations,id',
        ]);

        $poc->update($data);
        return redirect()->route('radio.admin.pocs.index')->with('ok', 'POC updated.');
    }

    /** Delete POC */
    public function destroy(Poc $poc)
    {
        $poc->delete();
        return redirect()->route('radio.admin.pocs.index')->with('ok', 'POC deleted.');
    }
}