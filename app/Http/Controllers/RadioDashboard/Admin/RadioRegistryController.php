<?php

namespace App\Http\Controllers\RadioDashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Station;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\JsonResponse;

class RadioRegistryController extends Controller
{
    public function index()
{
    $departments = DB::table('ht_departements')->select('id','name')->orderBy('name')->get();

    $canJoin = Schema::hasColumns('radio_stations', ['department_id','arrondissement_id','commune_id']);

    if ($canJoin) {
        $stations = DB::table('radio_stations as s')
            ->leftJoin('ht_departements as d', 'd.id', '=', 's.department_id')
            ->leftJoin('ht_arrondissements as a', 'a.id', '=', 's.arrondissement_id')
            ->leftJoin('ht_communes as c', 'c.id', '=', 's.commune_id')
            ->select(
                's.id','s.name','s.frequency','s.frequency_status','s.on_air','s.created_at',
                'd.name as department_name','a.name as arrondissement_name','c.name as commune_name'
            )
            ->orderByDesc('s.created_at')
            ->get();
    } else {
        // Fallback: no geo columns yet, show basic fields
        $stations = DB::table('radio_stations as s')
            ->select('s.id','s.name','s.frequency','s.frequency_status','s.on_air','s.created_at')
            ->orderByDesc('s.created_at')
            ->get()
            ->map(function ($row) {
                $row->department_name = '';
                $row->arrondissement_name = '';
                $row->commune_name = '';
                return $row;
            });
    }

    return view('radio_dashboard.admin.stations_create', compact('departments','stations'));
}
     public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:120',
            'department_id'     => 'required|integer|exists:ht_departements,id',
            'arrondissement_id' => 'required|integer|exists:ht_arrondissements,id',
            'commune_id'        => 'required|integer|exists:ht_communes,id',
            'frequency'         => 'nullable|string|max:30',
            'frequency_status'  => 'required|in:Acquired,Not acquired',
            'notes'             => 'nullable|string|max:1000',
            // NOTE: do not validate on_air here; we coerce it explicitly
        ]);

        // Checkbox → boolean (true if checked, false if not present)
        $validated['on_air'] = $request->boolean('on_air');

        try {
            Station::create($validated);
        } catch (\Throwable $e) {
            Log::error('Station create failed', [
                'error'   => $e->getMessage(),
                'payload' => $validated,
            ]);

            return back()
                ->withInput()
                ->with('open_station_modal', true)
                ->withErrors(['store' => 'Could not save station. Check logs for details.']);
        }

        return redirect()
            ->route('radio.admin.stations.index')
            ->with('ok', 'Station saved.'); // <-- matches your Blade
    }

    /** Toggle on-air (optional) */
    public function toggle(Station $station)
    {
        $station->on_air = ! $station->on_air;
        $station->save();

        return back()->with('ok', 'On-Air toggled.');
    }

    
public function arrondissements($departmentId): JsonResponse
{
    try {
        // detect FK column
        $fk = Schema::hasColumn('ht_arrondissements', 'department_id')
            ? 'department_id'
            : (Schema::hasColumn('ht_arrondissements', 'departement_id') ? 'departement_id' : null);

        if (!$fk) {
            return response()->json(['error' => 'FK column not found on ht_arrondissements'], 500);
        }

        // detect name column
        $nameCol = Schema::hasColumn('ht_arrondissements', 'name') ? 'name'
                  : (Schema::hasColumn('ht_arrondissements', 'arrondissement_nom') ? 'arrondissement_nom'
                  : (Schema::hasColumn('ht_arrondissements', 'libelle') ? 'libelle' : null));

        if (!$nameCol) {
            return response()->json(['error' => 'Name column not found on ht_arrondissements'], 500);
        }

        $rows = DB::table('ht_arrondissements')
            ->where($fk, $departmentId)
            ->orderBy($nameCol)
            ->get(['id', DB::raw("$nameCol as name")]);

        return response()->json($rows);
    } catch (\Throwable $e) {
        // log so you can see the true cause in storage/logs/laravel.log
        \Log::error('Arrondissements API failed', ['dept' => $departmentId, 'msg' => $e->getMessage()]);
        return response()->json(['error' => 'Server error'], 500);
    }
}

public function communes($arrondissementId): JsonResponse
{
    try {
        // detect FK column
        $fk = Schema::hasColumn('ht_communes', 'arrondissement_id')
            ? 'arrondissement_id'
            : (Schema::hasColumn('ht_communes', 'id_arrondissement') ? 'id_arrondissement' : null);

        if (!$fk) {
            return response()->json(['error' => 'FK column not found on ht_communes'], 500);
        }

        // detect name column
        $nameCol = Schema::hasColumn('ht_communes', 'name') ? 'name'
                  : (Schema::hasColumn('ht_communes', 'commune_nom') ? 'commune_nom'
                  : (Schema::hasColumn('ht_communes', 'libelle') ? 'libelle' : null));

        if (!$nameCol) {
            return response()->json(['error' => 'Name column not found on ht_communes'], 500);
        }

        $rows = DB::table('ht_communes')
            ->where($fk, $arrondissementId)
            ->orderBy($nameCol)
            ->get(['id', DB::raw("$nameCol as name")]);

        return response()->json($rows);
    } catch (\Throwable $e) {
        \Log::error('Communes API failed', ['arr' => $arrondissementId, 'msg' => $e->getMessage()]);
        return response()->json(['error' => 'Server error'], 500);
    }
}
}