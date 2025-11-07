<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Computer;

class ComputerController extends Controller
{
    /**
     * Show the form for creating a new computer entry.
     */
    public function create()
    {
        $campuses = DB::table('tbl_form_campus')
                       ->orderBy('campus')
                       ->pluck('campus', 'id');

        return view('admin.computers',[
        'campuses'   => $campuses,
        ]);
    }

    /**
     * Store a newly created computer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hostname'              => 'required|string|max:255',
            'location'             => 'nullable|string|max:100',
            'serial_number'         => 'nullable|string|max:100',
            'manufacturer'          => 'nullable|string|max:100',
            'model'                 => 'nullable|string|max:100',
            'bios_version'          => 'nullable|string|max:100',
            'cpu_model'             => 'nullable|string|max:100',
            'cpu_cores'             => 'nullable|integer',
            'ram_gb'                => 'nullable|integer',
            'storage_type'          => 'nullable|string|max:50',
            'storage_capacity_gb'   => 'nullable|integer',
            'operating_system'      => 'nullable|string|max:100',
            'os_version'            => 'nullable|string|max:100',
            'installed_applications'=> 'nullable|string',
            'assigned_user_id'         => 'nullable|string|max:255',
            'ministry'            => 'nullable|string|max:100',
            'purchase_date'         => 'nullable|date',
            'warranty_expiration'   => 'nullable|date',
        ]);

        Computer::create($validated);

        return redirect()
            ->route('admin.computers')
            ->with('success', 'Computer details saved successfully.');
    }
}