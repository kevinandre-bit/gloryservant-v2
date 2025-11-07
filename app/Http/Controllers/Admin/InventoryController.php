<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\InventoryController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use Illuminate\Http\RedirectResponse;

class InventoryController extends Controller
{
    /**
     * Show the form for creating a new equipment entry.
     */
    public function create()
    {   
        // pull from tbl_equipment, newest acquired first, 15 per page
        $equipment = Inventory::orderBy('acquired_at', 'desc')
                              ->paginate(15);
        // static categories (from config) or dynamic as above

        $campuses = DB::table('tbl_form_campus')
                       ->orderBy('campus')
                       ->pluck('campus', 'id');
     // 2) Static categories (since you hard-code these in the form)
        $categories = [
            'Audio/Visual',
            'Networking & Telecom',
            'Power Equipment',
            'Tools & Maintenance',
            'Furniture & Fixtures',
            'Mobile Devices',
            'Safety & Emergency',
            'Software & Licenses',
        ];

        return view('admin.inventory-equipment', [
        'equipment'   => $equipment,
        'categories' => $categories,
        'campuses'   => $campuses,
        ]);
    }

    /**
     * Store a newly created equipment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
        'name'           => 'required|string|max:255',
        'serial_number'  => 'required|string|max:100|unique:tbl_equipment,serial_number',
        'description'    => 'nullable|string',
        'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'category'       => 'required|string|max:100',
        'location'       => 'required|string|max:100',
        'spec_loc'       => 'nullable|string|max:255',
        'status'         => 'required|in:Active,In Repair,Retired',
        'acquired_at'    => 'nullable|date',
        'quantity'       => 'required|integer|min:1',
        'cost'           => 'nullable|numeric|min:0',
        ]);

        // 2) Handle photo upload into public/assets/images/equipment_photos
        if ($request->hasFile('photo')) {
            $file      = $request->file('photo');
            $ext       = strtolower($file->getClientOriginalExtension());
            $filename  = 'equip_'.time().'_'.substr(sha1($file->getClientOriginalName().uniqid('', true)),0,8).'.'.$ext;
            $destPath  = public_path('assets2/images/equipment_photos');

            if (!is_dir($destPath)) {
                @mkdir($destPath, 0755, true);
            }

            $file->move($destPath, $filename);
            $validated['photo'] = $filename;
        }

        // 3) Persist to the database
        Inventory::create($validated);

        // 4) Redirect back to your listing with a success message
        return redirect()
            ->route('admin.inventory-equipment')
            ->with('success', 'Equipment registered successfully.');
    }
    /**
 * Remove the specified equipment from storage.
 */
public function destroy(int $id): RedirectResponse
{
    $item = Inventory::findOrFail($id);
    $item->delete();

    return redirect()
        ->route('admin.inventory-equipment')
        ->with('success', 'Equipment deleted successfully.');
}
}
