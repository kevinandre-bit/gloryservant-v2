<?php

namespace App\Http\Controllers\RadioDashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SitesDirectoryController extends Controller
{
    /**
     * List sites + show modal form (departments come from reference table).
     */
    public function index()
    {
        // Reference data (for the modal's first dropdown)
        $departments = DB::table('ht_departements')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Existing sites (join for readable names)
        $sites = DB::table('radio_sites as s')
            ->leftJoin('ht_departements as d', 'd.id', '=', 's.department_id')
            ->leftJoin('ht_arrondissements as a', 'a.id', '=', 's.arrondissement_id')
            ->leftJoin('ht_communes as c', 'c.id', '=', 's.commune_id')
            ->select([
                's.id',
                's.nickname',
                'd.name as department_name',
                'a.name as arrondissement_name',
                'c.name as commune_name',
                's.owner',
                's.rep_name',
                's.rep_phone',
                's.rep_email',
                's.contract_start',
                's.contract_end',
                's.contract_link',
            ])
            ->orderBy('d.name')
            ->orderBy('c.name')
            ->orderBy('s.nickname')
            ->get();

        // NOTE: success message is pulled from the session in the Blade via session('success')
        return view('radio_dashboard.admin.sites_index', [
            'departments' => $departments,
            'sites'       => $sites,
        ]);
    }

    /**
     * Create a site (stores in radio_sites), generates a readable nickname, flashes success.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'department_id'     => ['required','integer','exists:ht_departements,id'],
            'arrondissement_id' => ['required','integer','exists:ht_arrondissements,id'],
            'commune_id'        => ['required','integer','exists:ht_communes,id'],

            'owner'     => ['required','string','max:255'],

            'rep_name'  => ['nullable','string','max:255'],
            'rep_phone' => ['nullable','string','max:100'],
            'rep_email' => ['nullable','email','max:255'],

            'contract_start' => ['nullable','date'],
            'contract_end'   => ['nullable','date','after_or_equal:contract_start'],
            'contract_link'  => ['nullable','url','max:1000'],

            'notes' => ['nullable','string','max:2000'],
        ]);

        // Build a readable unique nickname like "Commune Dept" (slugged), de-duplicated if needed
        $depName = DB::table('ht_departements')->where('id', $data['department_id'])->value('name');
        $comName = DB::table('ht_communes')->where('id', $data['commune_id'])->value('name');

        $baseNickname = trim(($comName ?: 'Site').' '.($depName ?: ''));
        $slug = Str::slug(str_replace("'", '', $baseNickname)); // e.g. les-cayes-sud

        // Ensure uniqueness by appending -2, -3, ... if same slug exists
        $nickname = $slug;
        $suffix = 2;
        while (
            DB::table('radio_sites')->where('nickname', $nickname)->exists()
        ) {
            $nickname = $slug.'-'.$suffix;
            $suffix++;
        }

        DB::table('radio_sites')->insert([
            'department_id'     => $data['department_id'],
            'arrondissement_id' => $data['arrondissement_id'],
            'commune_id'        => $data['commune_id'],

            'owner'     => $data['owner'],

            'rep_name'  => $data['rep_name']  ?? null,
            'rep_phone' => $data['rep_phone'] ?? null,
            'rep_email' => $data['rep_email'] ?? null,

            'contract_start' => $data['contract_start'] ?? null,
            'contract_end'   => $data['contract_end']   ?? null,
            'contract_link'  => $data['contract_link']  ?? null,

            'notes'    => $data['notes'] ?? null,
            'nickname' => $nickname,

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('radio.admin.sites.index')
            ->with('success', "Site saved as “{$nickname}”.");
    }

    /**
     * JSON: arrondissements by department
     */
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