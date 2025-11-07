<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class VolunteersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /** Build a safe select list based on actual columns */
    private function selectColumns(): array
    {
        $select = [
            'p.id',
            'p.firstname', 'p.mi', 'p.lastname',
            'cd.idno',
            'cd.campus',
        ];

        // Email: prefer p.emailaddress, else p.email
        if (Schema::hasColumn('tbl_people', 'emailaddress')) {
            $select[] = 'p.emailaddress as email';
        } elseif (Schema::hasColumn('tbl_people', 'email')) {
            $select[] = 'p.email as email';
        }

        // Employment status
        if (Schema::hasColumn('tbl_campus_data', 'employmentstatus')) {
            $select[] = 'cd.employmentstatus';
        } elseif (Schema::hasColumn('tbl_people', 'employmentstatus')) {
            $select[] = 'p.employmentstatus';
        }

        return $select;
    }

    public function index(Request $request)
    {
        $request->validate([
            'q'       => ['nullable','string','max:200'],
            'campus'  => ['nullable','string','max:100'],
        ]);

        $select = $this->selectColumns();

        // Detect schema
        $hasPeopleEmailAddr = Schema::hasColumn('tbl_people', 'emailaddress');
        $hasPeopleEmail     = Schema::hasColumn('tbl_people', 'email');
        $hasPeopleEmp       = Schema::hasColumn('tbl_people', 'employmentstatus');

        $hasCdIdno          = Schema::hasColumn('tbl_campus_data', 'idno');
        $hasCdCampus        = Schema::hasColumn('tbl_campus_data', 'campus');
        $hasCdEmp           = Schema::hasColumn('tbl_campus_data', 'employmentstatus');

        $q = DB::table('tbl_people as p')
            ->leftJoin('tbl_campus_data as cd', 'cd.reference', '=', 'p.id')
            ->select($select);

        if ($request->filled('q')) {
            $term = '%'.$request->q.'%';
            $q->where(function ($w) use ($term, $hasPeopleEmailAddr, $hasPeopleEmail, $hasPeopleEmp, $hasCdIdno, $hasCdCampus, $hasCdEmp) {
                $w->where('p.firstname', 'like', $term)
                  ->orWhere('p.lastname',  'like', $term);

                if ($hasCdIdno)    { $w->orWhere('cd.idno', 'like', $term); }
                if ($hasCdCampus)  { $w->orWhere('cd.campus', 'like', $term); }

                if ($hasPeopleEmailAddr) { $w->orWhere('p.emailaddress', 'like', $term); }
                if ($hasPeopleEmail)     { $w->orWhere('p.email', 'like', $term); }

                if ($hasCdEmp)     { $w->orWhere('cd.employmentstatus', 'like', $term); }
                if ($hasPeopleEmp) { $w->orWhere('p.employmentstatus', 'like', $term); }
            });
        }

        if ($request->filled('campus') && $hasCdCampus) {
            $q->where('cd.campus', $request->campus);
        }

        $volunteers = $q->orderBy('p.lastname')->orderBy('p.firstname')->get();

        // Campus options (form list) and distinct campuses from data table
        $campusOptions = DB::table('tbl_form_campus')
            ->whereNotNull('campus')->orderBy('campus')
            ->pluck('campus')->filter()->values();

        return view('volunteers.index', [
            'volunteers'     => $volunteers,
            'campuses'       => DB::table('tbl_campus_data')->distinct()->orderBy('campus')->pluck('campus')->filter()->values(),
            'campusOptions'  => $campusOptions,
        ]);
    }

    public function edit($id)
    {
        $select = $this->selectColumns();

        $vol = DB::table('tbl_people as p')
            ->leftJoin('tbl_campus_data as cd', 'cd.reference', '=', 'p.id')
            ->where('p.id', $id)
            ->select($select)
            ->first();

        if (!$vol) {
            return redirect()->route('volunteers.index')->with('error', 'Volunteer not found.');
        }

        $campusOptions = DB::table('tbl_form_campus')
            ->whereNotNull('campus')->orderBy('campus')
            ->pluck('campus')->filter()->values();

        return view('volunteers.edit', [
            'vol'           => $vol,
            'campusOptions' => $campusOptions,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Normalize input
        $request->merge([
            'firstname'        => trim((string) $request->firstname),
            'mi'               => trim((string) $request->mi),
            'lastname'         => trim((string) $request->lastname),
            'email'            => trim((string) $request->email),
            'idno'             => trim((string) $request->idno),
            'campus'           => trim((string) $request->campus),
            'employmentstatus' => trim((string) $request->employmentstatus),
        ]);

        // Validate
        $request->validate([
            'firstname'        => ['required','string','max:100'],
            'mi'               => ['nullable','string','max:10'],
            'lastname'         => ['required','string','max:100'],
            'email'            => ['nullable','email','max:255'],
            'idno'             => [
                'required','string','max:50',
                Rule::unique('tbl_campus_data','idno')->ignore($id, 'reference'),
            ],
            'employmentstatus' => ['nullable', Rule::in(['Active','Inactive'])],
            'campus'           => [
                'nullable','string','max:100',
                Rule::exists('tbl_form_campus', 'campus'),
            ],
        ], [
            'idno.unique'         => 'This ID number is already used by another volunteer.',
            'employmentstatus.in' => 'Status must be Active or Inactive.',
            'campus.exists'       => 'Please choose a valid campus from the list.',
        ]);

        // Detect schema
        $hasPeopleEmailAddr   = Schema::hasColumn('tbl_people', 'emailaddress');
        $hasPeopleEmail       = Schema::hasColumn('tbl_people', 'email');
        $hasPeopleEmpStatus   = Schema::hasColumn('tbl_people', 'employmentstatus');

        $hasCampusEmpStatus   = Schema::hasColumn('tbl_campus_data', 'employmentstatus');

        $peopleHasUpdatedAt   = Schema::hasColumn('tbl_people', 'updated_at');
        $campusHasUpdatedAt   = Schema::hasColumn('tbl_campus_data', 'updated_at');
        $campusHasCreatedAt   = Schema::hasColumn('tbl_campus_data', 'created_at');

        $usersHasUpdatedAt    = Schema::hasColumn('users', 'updated_at');

        DB::beginTransaction();
        try {
            // --- tbl_people update ---
            $peopleUpdate = [
                'firstname' => $request->firstname,
                'mi'        => $request->mi,
                'lastname'  => $request->lastname,
            ];
            if ($hasPeopleEmailAddr) {
                $peopleUpdate['emailaddress'] = $request->email;
            } elseif ($hasPeopleEmail) {
                $peopleUpdate['email'] = $request->email;
            }
            if ($hasPeopleEmpStatus && $request->filled('employmentstatus')) {
                $peopleUpdate['employmentstatus'] = $request->employmentstatus;
            }
            if ($peopleHasUpdatedAt) {
                $peopleUpdate['updated_at'] = now();
            }

            DB::table('tbl_people')->where('id', $id)->update($peopleUpdate);

            // --- tbl_campus_data upsert (by reference) ---
            $exists = DB::table('tbl_campus_data')->where('reference', $id)->first();

            $campusUpdate = [
                'idno'      => $request->idno,
                'campus'    => $request->campus,
                'reference' => $id,
            ];
            if ($hasCampusEmpStatus && $request->filled('employmentstatus')) {
                $campusUpdate['employmentstatus'] = $request->employmentstatus;
            }
            if ($campusHasUpdatedAt) {
                $campusUpdate['updated_at'] = now();
            }

            if ($exists) {
                DB::table('tbl_campus_data')->where('reference', $id)->update($campusUpdate);
            } else {
                if ($campusHasCreatedAt) {
                    $campusUpdate['created_at'] = now();
                }
                DB::table('tbl_campus_data')->insert($campusUpdate);
            }

            // --- users table sync (reference = people.id) ---
            if ($request->filled('employmentstatus')) {
                $userStatus = $request->employmentstatus === 'Active' ? 1 : 0;
                $usersUpdate = ['status' => $userStatus];
                if ($usersHasUpdatedAt) {
                    $usersUpdate['updated_at'] = now();
                }
                DB::table('users')->where('reference', $id)->update($usersUpdate);
            }

            DB::commit();

            return redirect()
                ->route('volunteers.edit', $id)
                ->with('success', 'Volunteer information updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return redirect()
                ->route('volunteers.edit', $id)
                ->withInput()
                ->with('error', 'Could not save changes. '.$e->getMessage());
        }
    }
}
