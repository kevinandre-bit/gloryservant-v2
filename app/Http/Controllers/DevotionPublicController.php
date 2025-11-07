<?php
// app/Http/Controllers/DevotionPublicController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // 👈 add this
use Illuminate\Validation\Rule;
use Carbon\Carbon; 

class DevotionPublicController extends Controller
{
    public function create()
    {
        return view('devotion.public-submit');
    }

   public function index(Request $request)
{
    // Default range: last Saturday -> today
    $today        = now()->startOfDay();
    $lastSaturday = $today->copy()->previous(Carbon::SATURDAY);

    if (!$request->filled('date_from')) {
        $request->merge(['date_from' => $lastSaturday->toDateString()]);
    }
    if (!$request->filled('date_to')) {
        $request->merge(['date_to' => $today->toDateString()]);
    }

    // Validate filters
    $request->validate([
        'campus'     => ['nullable','string','max:100'],
        'department' => ['nullable','string','max:100'],
        'ministry'   => ['nullable','string','max:100'],
        'person'     => ['nullable','integer','exists:tbl_people,id'],
        'date_from'  => ['nullable','date'],
        'date_to'    => ['nullable','date','after_or_equal:date_from'],
        'q'          => ['nullable','string','max:200'],
    ]);

    // Days in range (inclusive)
    $startDate = Carbon::parse($request->date_from)->startOfDay();
    $endDate   = Carbon::parse($request->date_to)->endOfDay();
    $daySpan   = max($startDate->diffInDays($endDate) + 1, 1);

    // Schema checks
    $hasCdCampusId   = Schema::hasColumn('tbl_campus_data', 'campus_id');
    $hasCdCampusText = Schema::hasColumn('tbl_campus_data', 'campus');
    $hasCdDept       = Schema::hasColumn('tbl_campus_data', 'department');
    $hasCdMin        = Schema::hasColumn('tbl_campus_data', 'ministry');
    $hasFormCampus   = Schema::hasTable('tbl_form_campus');
    $hasFormCampusId = $hasFormCampus && Schema::hasColumn('tbl_form_campus', 'id');
    $hasFormCampusNm = $hasFormCampus && Schema::hasColumn('tbl_form_campus', 'campus');

    // ------- Table rows -------
    $q = DB::table('tbl_people_devotion as d')
        ->leftJoin('tbl_people as p', 'p.id', '=', 'd.reference')
        ->leftJoin('tbl_campus_data as cd', 'cd.reference', '=', 'p.id');

    if ($hasCdCampusId && $hasFormCampusId) {
        $q->leftJoin('tbl_form_campus as cf', 'cf.id', '=', 'cd.campus_id');
    } elseif ($hasCdCampusText && $hasFormCampusNm) {
        $q->leftJoin('tbl_form_campus as cf', 'cf.campus', '=', 'cd.campus');
    }

    $select = [
        'd.id','d.devotion_date','d.devotion_text','d.status','d.created_at',
        'p.firstname','p.lastname','p.mi',
        $hasCdDept ? 'cd.department' : DB::raw('NULL as department'),
        $hasCdMin  ? 'cd.ministry'   : DB::raw('NULL as ministry'),
    ];
    if ($hasFormCampusNm)      { $select[] = DB::raw('cf.campus as campus_name'); }
    elseif ($hasCdCampusText)  { $select[] = DB::raw('cd.campus as campus_name'); }
    else                       { $select[] = DB::raw('NULL as campus_name'); }

    $q->select($select);

    // Apply filters to rows
    if ($request->filled('campus')) {
        $val = trim($request->campus);
        if ($hasFormCampusNm)     $q->where('cf.campus', $val);
        elseif ($hasCdCampusText) $q->where('cd.campus', $val);
    }
    if ($request->filled('department') && $hasCdDept) $q->where('cd.department', $request->department);
    if ($request->filled('ministry')   && $hasCdMin)  $q->where('cd.ministry',   $request->ministry);
    if ($request->filled('person'))                  $q->where('d.reference', (int)$request->person);

    $q->whereBetween('d.devotion_date', [$startDate->toDateString(), $endDate->toDateString()]);
    if ($request->filled('q')) {
        $q->where('d.devotion_text', 'like', '%'.$request->q.'%');
    }

    $devotions = $q->orderByDesc('d.devotion_date')
                   ->orderByDesc('d.created_at')
                   ->get();

    // ------- People targeted (scope for goal & person select) -------
    $employees = DB::table('tbl_people as p')
        ->join('tbl_campus_data as cd', 'p.id', '=', 'cd.reference');

    if ($request->filled('campus')) {
        $val = trim($request->campus);
        if ($hasFormCampusNm && $hasCdCampusId) {
            $employees->join('tbl_form_campus as cf', 'cf.id', '=', 'cd.campus_id')
                      ->where('cf.campus', $val);
        } elseif ($hasCdCampusText) {
            $employees->where('cd.campus', $val);
        }
    }
    if ($request->filled('department') && $hasCdDept) $employees->where('cd.department', $request->department);
    if ($request->filled('ministry')   && $hasCdMin)  $employees->where('cd.ministry',   $request->ministry);
    // Build peopleOptions BEFORE narrowing by person (so dropdown shows all scoped people)
    $peopleOptions = (clone $employees)
        ->select('p.id','p.firstname','p.lastname','p.mi')
        ->orderBy('p.firstname')->orderBy('p.lastname')
        ->get()
        ->map(function ($u) {
            $name = trim(($u->firstname ?? '').' '.($u->mi ?? '').' '.($u->lastname ?? ''));
            return (object)['id' => $u->id, 'name' => ($name !== '' ? $name : 'Unknown')];
        });

    // If a specific person is chosen, restrict the scope for goal math
    if ($request->filled('person')) {
        $employees->where('p.id', (int)$request->person);
    }

    $employeeIds    = $employees->pluck('p.id');
    $targetedPeople = $employeeIds->count();

    // ------- Totals & Goal -------
    $goal       = $targetedPeople * $daySpan; // one post per person per day in range
    // --- existing variables this code expects ---
// $employeeIds, $targetedPeople, $startDate, $endDate, $daySpan

// Total posts (unchanged – posts-based)
$totalPosts = DB::table('tbl_people_devotion as d')
    ->whereBetween('d.devotion_date', [$startDate->toDateString(), $endDate->toDateString()])
    ->when($targetedPeople > 0, fn($qq) => $qq->whereIn('d.reference', $employeeIds))
    ->count();

// Per-person counts within the range (for people-based metrics)
$perPerson = DB::table('tbl_people_devotion as d')
    ->whereBetween('d.devotion_date', [$startDate->toDateString(), $endDate->toDateString()])
    ->when($targetedPeople > 0, fn($qq) => $qq->whereIn('d.reference', $employeeIds))
    ->groupBy('d.reference')
    ->select('d.reference', DB::raw('COUNT(*) as posts'))
    ->get();

$uniquePosters = $perPerson->count();
$zeros         = max($targetedPeople - $uniquePosters, 0);     // people with 0 posts
$atLeastDays   = $perPerson->where('posts', '>=', $daySpan)->count(); // met daily goal

// Helpers
$pct = fn($num, $den) => $den > 0 ? round(($num / $den) * 100) : 0;

// POSTS-BASED card (Total Devotions)
$goalPosts    = $targetedPeople * $daySpan;             // people × days
$achievedPct  = $pct($totalPosts, max($goalPosts, 1));  // achieved (for green badge)
$missingPct   = 100 - $achievedPct;                     // missing (for purple bar)
$leftPosts    = max($goalPosts - $totalPosts, 0);       // posts missing to 100%

// PEOPLE-BASED cards (No Devotions / Daily Goal)
$noDevotionPercent = $pct($zeros, max($targetedPeople, 1));        // share with 0 posts
$noDevotionLeft    = $zeros;                                        // people who still need 1+

$sixPlusPercent    = $pct($atLeastDays, max($targetedPeople, 1));   // share who met goal
$sixPlusLeft       = max($targetedPeople - $atLeastDays, 0);        // people still missing goal

$summary = [
    // Total Devotions (posts-based)
    'total'      => $totalPosts,
    'growth'     => $achievedPct.'%',     // green badge (achieved % of posts goal)
    'leftToGoal' => $leftPosts,           // posts missing to reach 100%
    'progress'   => $missingPct.'%',      // purple bar shows missing %

    // People with no devotions (people-based)
    'noDevotion'        => $zeros,
    'noDevotionPercent' => $noDevotionPercent.'%', // red badge & bar
    'noDevotionLeft'    => $noDevotionLeft,        // people who still need to post once

    // People meeting daily goal (people-based)
    'sixPlus'           => $atLeastDays,
    'sixPlusPercent'    => $sixPlusPercent.'%',    // green badge (achieved % of targeted)
    'sixPlusLeft'       => $sixPlusLeft,           // people still missing the goal

    // Context
    'days'   => $daySpan,
    'people' => $targetedPeople,
    'goal'   => $goalPosts,
];

    // Dropdown options
    if ($hasFormCampusNm) {
        $campusOptions = DB::table('tbl_form_campus')
            ->whereNotNull('campus')->orderBy('campus')
            ->pluck('campus')->filter()->values();
    } elseif ($hasCdCampusText) {
        $campusOptions = DB::table('tbl_campus_data')
            ->whereNotNull('campus')->distinct()->orderBy('campus')
            ->pluck('campus')->filter()->values();
    } else {
        $campusOptions = collect();
    }

    $departmentOptions = $hasCdDept
        ? DB::table('tbl_campus_data')->whereNotNull('department')->distinct()->orderBy('department')->pluck('department')->filter()->values()
        : collect();

    $ministryOptions = $hasCdMin
        ? DB::table('tbl_campus_data')->whereNotNull('ministry')->distinct()->orderBy('ministry')->pluck('ministry')->filter()->values()
        : collect();

    return view('devotion.public-index', [
        'devotions'          => $devotions,
        'campusOptions'      => $campusOptions,
        'departmentOptions'  => $departmentOptions,
        'ministryOptions'    => $ministryOptions,
        'peopleOptions'      => $peopleOptions,  // for Select2 person dropdown
        'summary'            => $summary,
    ]);
}
    public function store(Request $request)
    {
        // Normalize date to Y-m-d to match DB DATE column
        $request->merge([
            'devotion_date' => date('Y-m-d', strtotime($request->devotion_date ?? 'now')),
        ]);

        $request->validate([
            'idno'           => ['required','string','max:50'],
            'devotion_date'  => [
                'required','date',
                Rule::unique('tbl_people_devotion', 'devotion_date')
                    ->where(fn($q) => $q->where('idno', $request->idno)),
            ],
            'devotion_text'  => ['required','string','max:5000'],
            'hp'             => ['nullable','size:0'],
        ], [
            'devotion_date.unique' => 'A devotion for this date is already submitted for this ID.',
            'hp.size'              => 'Invalid submission.',
        ]);

        $idno = trim($request->idno);

        // Resolve person by joining campus data (idno) to people (names)
        $person = DB::table('tbl_campus_data as cd')
            ->join('tbl_people as p', 'p.id', '=', 'cd.reference')
            ->where('cd.idno', $idno)
            ->select('p.id as reference', 'p.firstname', 'p.lastname', 'p.mi', 'cd.idno')
            ->first();

        if (!$person) {
            return back()
                ->withInput()
                ->withErrors(['idno' => 'ID number not found. Please double-check your ID.']);
        }

        $employee = trim(
            ($person->lastname ?? '') . ', ' .
            ($person->firstname ?? '') .
            (isset($person->mi) && $person->mi !== '' ? ' ' . $person->mi : '')
        );
        if ($employee === ',' || $employee === ', ') $employee = 'Unknown';

        try {
            DB::table('tbl_people_devotion')->insert([
                'devotion_date' => $request->devotion_date,   // Y-m-d
                'devotion_text' => $request->devotion_text,
                'reference'     => $person->reference,        // tbl_people.id
                'idno'          => $idno,
                'employee'      => $employee,
                'status'        => 'Pending',
                'comment'       => '',
                'archived'      => 0,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ((int)($e->errorInfo[1] ?? 0) === 1062) {
                return back()
                    ->withInput()
                    ->withErrors(['devotion_date' => 'A devotion for this date is already submitted for this ID.']);
            }
            return back()
                ->withInput()
                ->withErrors(['idno' => 'Could not save devotion. Please try again.']);
        }

        return redirect()
            ->route('devotion.public.create')
            ->with('success', 'Devotion submitted successfully! Thank you.');
    }
}