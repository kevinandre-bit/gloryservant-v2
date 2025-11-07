<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InsightGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportDashboardController extends Controller
{
    public function index(Request $req, InsightGenerator $insights)
{
    // ----------------- Filters -----------------
    $range     = strtoupper($req->input('range', 'WEEK')); // WEEK|MONTH|QUARTER|YEAR|CUSTOM
    $teamId    = $req->input('team_id');                   // nullable
    $personId  = $req->input('person_id');                 // nullable
    $fromInput = $req->input('from');                      // Y-m-d if CUSTOM
    $toInput   = $req->input('to');

    // Date window (inclusive YYYY-MM-DD)
    [$startDate, $endDate] = $this->resolveRange(
        $range,
        $fromInput ? Carbon::parse($fromInput) : null,
        $toInput   ? Carbon::parse($toInput)   : null
    );



    // ----------------- Reference lists (filters) -----------------
    $teams = DB::table('tbl_report_teams')
        ->where('active', 1)
        ->orderBy('name')
        ->get(['id','name']);

    $people = DB::table('tbl_report_people')
        ->when($teamId, fn($q) => $q->where('team_id', $teamId))
        ->orderBy('last_name')->orderBy('first_name')
        ->get(['id','first_name','last_name','team_id']);

    // ----------------- Metrics (defs) -----------------
    // Get all active metrics; include their category names
    $metrics = DB::table('tbl_report_metrics as m')
        ->leftJoin('tbl_report_categories as c','c.id','=','m.category_id')
        ->where('m.active',1)
        ->orderBy('m.name')
        ->get([
            'm.id','m.name','m.value_type','m.value_mode','m.status_set_id','m.weight',
            DB::raw('COALESCE(c.name,"Uncategorized") as category_name'),
        ]);



    // IMPORTANT: Attendance = all metrics whose category starts with "Attendance"
    $attendanceMetricIds = $metrics
        ->filter(fn($m) => stripos($m->category_name, 'Attendance') === 0)
        ->pluck('id')
        ->values()
        ->all();

    // ----------------- People scope -----------------
    $personScope = DB::table('tbl_report_people as p')
        ->when($teamId,  fn($q) => $q->where('p.team_id', $teamId))
        ->when($personId,fn($q) => $q->where('p.id', $personId))
        ->where(function($q){
            $q->whereNull('p.status')->orWhere('p.status','active');
        })
        ->pluck('p.id')
        ->all();

    if (empty($personScope)) {
        return view('admin.reports.report-dashboard', [
            'vm' => $this->emptyViewModel($range, $startDate, $endDate, $teams, $people)
        ]);
    }

    // ----------------- Raw events in date window -----------------
    // Collation-safe status mapping to avoid "Illegal mix of collations"
    $events = DB::table('tbl_report_metric_events as e')
        ->join('tbl_report_metrics as m','m.id','=','e.metric_id')
        ->leftJoin('tbl_report_status_set_items as ssi', function($j){
            $j->on('ssi.status_set_id','=','m.status_set_id')
              ->whereRaw('ssi.code COLLATE utf8mb4_unicode_ci = e.status COLLATE utf8mb4_unicode_ci');
        })
        ->join('tbl_report_people as p','p.id','=','e.person_id')
        ->leftJoin('tbl_report_teams as t','t.id','=','p.team_id')
        ->whereBetween('e.occurred_on', [$startDate, $endDate])
        ->whereIn('e.person_id', $personScope)
        ->get([
            'e.person_id','e.metric_id','e.occurred_on',
            'e.status','e.numeric_value',
            'm.value_mode','m.status_set_id','m.weight',
            'p.first_name','p.last_name','t.name as team_name',
            DB::raw('COALESCE(ssi.score, NULL) as status_score'),
        ]);
        // 0) Period size in weeks (fractional allowed)
$periodDays  = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
$weeksInPeriod = $periodDays / 7.0;

// 1) Attendance metrics (category starts with 'Attendance')
$attendanceMetricIds = $metrics
    ->filter(fn($m) => stripos($m->category_name, 'Attendance') === 0)
    ->pluck('id')->values()->all();

// 2) Weights (default 1)
$metricWeights = $metrics->keyBy('id')->map(function($m){
    return (float)($m->weight ?? 1);
});

// 3) Convert events to score_value (you already do this earlier; keep it)
foreach ($events as $ev) {
    if ($ev->value_mode === 'status_set') {
        $ev->score_value = is_null($ev->status_score) ? null : (float)$ev->status_score;
    } else {
        $ev->score_value = is_null($ev->numeric_value) ? null : (float)$ev->numeric_value;
    }
}

// 4) Build per-person, per-metric aggregates needed for normalized math
$perPM = [];      // $perPM[pid][mid] = ['sum'=>..., 'n'=>...]
$personTeam = []; // you already fill this elsewhere; ensure it's set here too
foreach ($events as $ev) {
    $personTeam[$ev->person_id] = $ev->team_name ?? '—';
    if (is_null($ev->score_value)) continue;
    $perPM[$ev->person_id][$ev->metric_id]['sum'] = ($perPM[$ev->person_id][$ev->metric_id]['sum'] ?? 0) + $ev->score_value;
    $perPM[$ev->person_id][$ev->metric_id]['n']   = ($perPM[$ev->person_id][$ev->metric_id]['n']   ?? 0) + 1;
}

// 5) Attendance KPI (normalized, weight-aware)
$attSumNorm = 0.0;
$attDenom   = 0.0; // expected instances across people × metrics
foreach ($personScope as $pid) {
    foreach ($attendanceMetricIds as $mid) {
        $expected = ($metricWeights[$mid] ?? 1.0) * $weeksInPeriod;
        if ($expected <= 0) continue;

        $sum = $perPM[$pid][$mid]['sum'] ?? 0.0;   // missing events => 0 score
        $attSumNorm += ($sum / $expected);        // normalized score already 0–100 if scores are 0–100
        $attDenom   += 100.0;                     // each expected slot contributes up to 100 per unit expected
    }
}
// In this normalization, we summed normalized scores per metric/person; to keep it 0–100,
// divide by number of person×metric “units”:
$attPct = ($attDenom > 0) ? round(($attSumNorm / ($attDenom/100.0)), 1) : null;

// 6) Members snapshot (attendance per person, normalized across all attendance metrics)
$members = []; // we’ll rebuild based on your previous $byPerson approach but weight-aware for att
$scoreOverall = []; // still need overall productivity avg (flat, as you had)

foreach ($events as $ev) {
    if (is_null($ev->score_value)) continue;
    $scoreOverall[$ev->person_id]['sum'] = ($scoreOverall[$ev->person_id]['sum'] ?? 0) + $ev->score_value;
    $scoreOverall[$ev->person_id]['n']   = ($scoreOverall[$ev->person_id]['n']   ?? 0) + 1;
}

foreach ($personScope as $pid) {
    $attPersonSum = 0.0;
    $attUnits     = 0.0;
    foreach ($attendanceMetricIds as $mid) {
        $expected = ($metricWeights[$mid] ?? 1.0) * $weeksInPeriod;
        if ($expected <= 0) continue;
        $sum = $perPM[$pid][$mid]['sum'] ?? 0.0;
        $attPersonSum += ($sum / $expected); // normalized 0–100
        $attUnits     += 1.0;                // count of attendance metrics considered
    }
    $attPerson = ($attUnits > 0) ? round($attPersonSum / $attUnits, 0) : null;

    $overall = $scoreOverall[$pid] ?? null;
    $score   = ($overall && $overall['n'] > 0) ? round($overall['sum'] / $overall['n'], 0) : null;

    $name = DB::table('tbl_report_people')->where('id',$pid)->value(DB::raw("CONCAT_WS(' ', first_name, last_name)")) ?: '—';
    $team = $personTeam[$pid] ?? '—';

    $members[] = [
        'id'    => $pid,                              // <-- add this
        'name'  => $name,
        'team'  => $team,
        'att'   => $attPerson,  // now weight-aware, penalizes missing entries
        'score' => $score,      // same as before (flat mean of submitted)
    ];
}

// 7) Attendance Trend (12 weeks, normalized weekly)
$attTrend = [];
foreach ($this->lastNWeeks(12, $endDate) as [$ws, $we, $label]) {
    $weekDays = Carbon::parse($ws)->diffInDays(Carbon::parse($we)) + 1;
    $weekUnits = $weekDays / 7.0; // should be 1, but keeps it robust

    // get attendance events for THIS week
    $rows = [];
    if (!empty($attendanceMetricIds)) {
        $rows = DB::table('tbl_report_metric_events as e')
            ->join('tbl_report_metrics as m','m.id','=','e.metric_id')
            ->leftJoin('tbl_report_status_set_items as ssi', function($j){
                $j->on('ssi.status_set_id','=','m.status_set_id')
                  ->whereRaw('ssi.code COLLATE utf8mb4_unicode_ci = e.status COLLATE utf8mb4_unicode_ci');
            })
            ->whereIn('e.metric_id', $attendanceMetricIds)
            ->whereBetween('e.occurred_on', [$ws, $we])
            ->whereIn('e.person_id', $personScope)
            ->get(['e.person_id','e.metric_id','e.status','e.numeric_value','m.value_mode','m.status_set_id',
                   DB::raw('COALESCE(ssi.score,NULL) as status_score')]);
    }

    // perPM for the week
    $perPMW = [];
    foreach ($rows as $r) {
        $sv = ($r->value_mode === 'status_set')
            ? (is_null($r->status_score) ? null : (float)$r->status_score)
            : (is_null($r->numeric_value) ? null : (float)$r->numeric_value);
        if ($sv === null) continue;
        $perPMW[$r->person_id][$r->metric_id]['sum'] = ($perPMW[$r->person_id][$r->metric_id]['sum'] ?? 0) + $sv;
        $perPMW[$r->person_id][$r->metric_id]['n']   = ($perPMW[$r->person_id][$r->metric_id]['n']   ?? 0) + 1;
    }

    // weekly normalized attendance
    $wkSum = 0.0; $wkUnits = 0.0;
    foreach ($personScope as $pid) {
        foreach ($attendanceMetricIds as $mid) {
            $expected = ($metricWeights[$mid] ?? 1.0) * $weekUnits; // ~weight*1
            if ($expected <= 0) continue;
            $sum = $perPMW[$pid][$mid]['sum'] ?? 0.0;
            $wkSum  += ($sum / $expected);
            $wkUnits+= 1.0;
        }
    }
    $wkAvg = ($wkUnits > 0) ? round($wkSum / $wkUnits, 1) : null;
    $attTrend[] = ['label'=>$label, 'avg'=>$wkAvg];
}

    // Per-event numeric score (status_set -> mapped score; number -> numeric_value)
    foreach ($events as $ev) {
        if ($ev->value_mode === 'status_set') {
            $ev->score_value = is_null($ev->status_score) ? null : (float)$ev->status_score;
        } else {
            $ev->score_value = is_null($ev->numeric_value) ? null : (float)$ev->numeric_value;
        }
    }

    // ----------------- KPIs -----------------
    // Attendance % (aggregate across ALL attendance metrics)
    $attPct = null;
    if (!empty($attendanceMetricIds)) {
        $attScores = collect($events)
            ->whereIn('metric_id', $attendanceMetricIds)
            ->pluck('score_value')
            ->filter(fn($v)=>$v!==null)
            ->all();
        if (!empty($attScores)) {
            $attPct = round(array_sum($attScores)/count($attScores), 1);
        }
    }

    // Composite (weighted by metric weight) - per person then averaged
    $metricWeights = $metrics->keyBy('id')->map->weight;
    $perPerson     = []; // [pid => ['sum'=>..., 'w_sum'=>...]]
    $personTeam    = [];
    foreach ($events as $ev) {
        $personTeam[$ev->person_id] = $ev->team_name ?? '—';
        if (is_null($ev->score_value)) continue;
        $w = (float)($metricWeights[$ev->metric_id] ?? 1);
        $perPerson[$ev->person_id]['sum']   = ($perPerson[$ev->person_id]['sum']   ?? 0) + ($ev->score_value * $w);
        $perPerson[$ev->person_id]['w_sum'] = ($perPerson[$ev->person_id]['w_sum'] ?? 0) + $w;
    }
    $personAvgs = [];
    foreach ($perPerson as $pid => $acc) {
        if (($acc['w_sum'] ?? 0) > 0) {
            $personAvgs[] = $acc['sum'] / $acc['w_sum'];
        }
    }
    $composite = !empty($personAvgs) ? round(array_sum($personAvgs)/count($personAvgs), 1) : null;

    // Avg Performance (flat mean of all score_value)
    $flatScores = collect($events)->pluck('score_value')->filter(fn($v)=>$v!==null)->values();
    $avgScore   = $flatScores->count() ? round($flatScores->avg(), 1) : null;

    // Counts
    $activeMembers = DB::table('tbl_report_people as p')
        ->when($teamId, fn($q)=>$q->where('p.team_id',$teamId))
        ->where(function($q){ $q->whereNull('p.status')->orWhere('p.status','active'); })
        ->count();

    $teamCount = $teamId ? 1 : DB::table('tbl_report_teams')->where('active',1)->count();

    // ----------------- Attendance Trend (12 weeks; ALL attendance metrics) -----------------
    $attTrend = [];
    foreach ($this->lastNWeeks(12, $endDate) as [$ws, $we, $label]) {
        if (!empty($attendanceMetricIds)) {
            $rows = DB::table('tbl_report_metric_events as e')
                ->join('tbl_report_metrics as m','m.id','=','e.metric_id')
                ->leftJoin('tbl_report_status_set_items as ssi', function($j){
                    $j->on('ssi.status_set_id','=','m.status_set_id')
                      ->whereRaw('ssi.code COLLATE utf8mb4_unicode_ci = e.status COLLATE utf8mb4_unicode_ci');
                })
                ->whereIn('e.metric_id', $attendanceMetricIds)
                ->whereBetween('e.occurred_on', [$ws, $we])
                ->whereIn('e.person_id', $personScope)
                ->get([
                    'e.status','e.numeric_value','m.value_mode','m.status_set_id',
                    DB::raw('COALESCE(ssi.score,NULL) as status_score')
                ]);

            $vals = [];
            foreach ($rows as $r) {
                if ($r->value_mode === 'status_set') {
                    $vals[] = is_null($r->status_score) ? null : (float)$r->status_score;
                } else {
                    $vals[] = is_null($r->numeric_value) ? null : (float)$r->numeric_value;
                }
            }
            $vals = array_values(array_filter($vals, fn($v)=>$v!==null));
            $attTrend[] = ['label'=>$label, 'avg'=> $vals ? round(array_sum($vals)/count($vals),1) : null];
        } else {
            $attTrend[] = ['label'=>$label, 'avg'=> null];
        }
    }

    // ----------------- Team comparison (composite avg per team) -----------------
    $teamAgg = [];
    foreach ($perPerson as $pid => $acc) {
        if (($acc['w_sum'] ?? 0) <= 0) continue;
        $team  = $personTeam[$pid] ?? '—';
        $score = $acc['sum'] / $acc['w_sum'];
        $teamAgg[$team]['sum'] = ($teamAgg[$team]['sum'] ?? 0) + $score;
        $teamAgg[$team]['n']   = ($teamAgg[$team]['n']   ?? 0) + 1;
    }
    $teamCompare = [];
    foreach ($teamAgg as $tn => $acc) {
        $teamCompare[] = ['team'=>$tn, 'score'=> round($acc['sum']/max(1,$acc['n']), 1)];
    }
    usort($teamCompare, fn($a,$b)=>$b['score'] <=> $a['score']);

    // ----------------- Category mix (weight %) -----------------
    $catWeight = []; $totalW = 0;
    foreach ($metrics as $m) {
        $w = (float)($m->weight ?? 1);
        $catWeight[$m->category_name] = ($catWeight[$m->category_name] ?? 0) + $w;
        $totalW += $w;
    }
    $categoryMix = [];
    foreach ($catWeight as $cat => $wSum) {
        $categoryMix[$cat] = $totalW > 0 ? round(($wSum / $totalW)*100) : 0;
    }

    // ----------------- Members snapshot (period) -----------------
    $byPerson = [];
    foreach ($events as $ev) {
        if (is_null($ev->score_value)) continue;

        $byPerson[$ev->person_id]['name'] = trim(($ev->first_name ?? '').' '.($ev->last_name ?? ''));
        $byPerson[$ev->person_id]['team'] = $personTeam[$ev->person_id] ?? '—';

        // Overall (productivity/performance) average for the person
        $byPerson[$ev->person_id]['score_sum'] = ($byPerson[$ev->person_id]['score_sum'] ?? 0) + $ev->score_value;
        $byPerson[$ev->person_id]['score_n']   = ($byPerson[$ev->person_id]['score_n']   ?? 0) + 1;

        // Attendance component: include ALL attendance metrics
        if (in_array((int)$ev->metric_id, $attendanceMetricIds, true)) {
            $byPerson[$ev->person_id]['att_sum'] = ($byPerson[$ev->person_id]['att_sum'] ?? 0) + $ev->score_value;
            $byPerson[$ev->person_id]['att_n']   = ($byPerson[$ev->person_id]['att_n']   ?? 0) + 1;
        }
    }

    $members = [];
    foreach ($byPerson as $pid => $acc) {
        $members[] = [
            'name'  => $acc['name'] ?: '—',
            'team'  => $acc['team'] ?: '—',
            'att'   => isset($acc['att_n']) ? round($acc['att_sum'] / max(1,$acc['att_n'])) : null,
            'score' => isset($acc['score_n']) ? round($acc['score_sum'] / max(1,$acc['score_n'])) : null,
        ];
    }

    $topPerformers = collect($members)->filter(fn($m)=>$m['score']!==null)
        ->sortByDesc('score')->take(5)->values()->all();

    $atRisk = collect($members)->filter(fn($m) => (($m['att'] ?? 100) < 60) || (($m['score'] ?? 100) < 60))
        ->values()->all();

    // ----------------- Smart Insights -----------------
    try {
        $smartInsights = $insights->fromDashboardDataset([
            'attendancePct' => array_map(fn($x)=>($x['avg'] ?? 0), $attTrend),
            'avgScore'      => $flatScores->take(12)->values()->all(),
            'teams'         => collect($teamCompare)->mapWithKeys(fn($t)=>[$t['team'] => ['score'=>$t['score']]])->all(),
            'members'       => $members,
            'categoryMix'   => $categoryMix,
            'activeMembers' => $activeMembers,
            'teamCount'     => $teamCount,
            'targetActive'  => 150,
        ]);
    } catch (\Throwable $e) {
        $smartInsights = [];
    }

    // ----------------- View Model -----------------
    $vm = [
        'filters' => [
            'range'     => $range,
            'team_id'   => $teamId,
            'person_id' => $personId,
            'start'     => $startDate,
            'end'       => $endDate,
        ],
        'kpis' => [
            'attendance'    => $attPct,
            'avgScore'      => $avgScore,
            'composite'     => $composite,
            'activeMembers' => $activeMembers,
            'teamCount'     => $teamCount,
        ],
        'trend' => [
            'labels'     => array_map(fn($t)=>$t['label'], $attTrend),
            'attendance' => array_map(fn($t)=>$t['avg'],   $attTrend),
        ],
        'teamsCompare'    => $teamCompare,
        'categoryMix'     => $categoryMix,
        'members'         => $members,
        'topPerformers'   => $topPerformers,
        'atRisk'          => $atRisk,
        'avgScoreHistory' => $flatScores->take(12)->values()->all(),
        'smartInsights'   => $smartInsights,
        'targetActive'    => 150,
        'teamsList'       => $teams,
        'peopleList'      => $people,
    ];

    return view('admin.reports.report-dashboard', compact('vm'));
}

    /**
     * Resolve a user-selected range into [startDate, endDate] (YYYY-MM-DD, inclusive).
     */
    private function resolveRange(string $range, ?Carbon $from, ?Carbon $to): array
    {
        $today = Carbon::today();

        switch ($range) {
            case 'MONTH':
                $start = $today->copy()->startOfMonth();
                $end   = $today->copy()->endOfMonth();
                break;
            case 'QUARTER':
                $start = $today->copy()->firstOfQuarter();
                $end   = $today->copy()->lastOfQuarter();
                break;
            case 'YEAR':
                $start = $today->copy()->startOfYear();
                $end   = $today->copy()->endOfYear();
                break;
            case 'CUSTOM':
                $start = $from ? $from->copy()->startOfDay() : $today->copy()->startOfWeek();
                $end   = $to   ? $to->copy()->endOfDay()     : $today->copy()->endOfWeek();
                break;
            case 'WEEK':
            default:
                $start = $today->copy()->startOfWeek();
                $end   = $today->copy()->endOfWeek();
                break;
        }

        return [$start->toDateString(), $end->toDateString()];
    }

    /**
     * Return the last N ISO weeks ending at $endDate (YYYY-MM-DD),
     * as an array of [weekStart, weekEnd, label] strings.
     */
    private function lastNWeeks(int $n, string $endDate): array
    {
        $end = Carbon::parse($endDate)->endOfWeek();
        $out = [];
        for ($i = $n - 1; $i >= 0; $i--) {
            $we = $end->copy()->subWeeks($i);
            $ws = $we->copy()->startOfWeek();
            $label = 'W'. $ws->format('W');
            $out[] = [$ws->toDateString(), $we->toDateString(), $label];
        }
        return $out;
    }

    /**
     * When no people are in scope, return an "empty but pretty" VM.
     */
    private function emptyViewModel(string $range, string $start, string $end, $teams, $people): array
    {
        return [
            'filters' => [
                'range'     => $range,
                'team_id'   => null,
                'person_id' => null,
                'start'     => $start,
                'end'       => $end,
            ],
            'kpis' => [
                'attendance'    => null,
                'avgScore'      => null,
                'composite'     => null,
                'activeMembers' => 0,
                'teamCount'     => 0,
            ],
            'trend' => [
                'labels'     => [],
                'attendance' => [],
            ],
            'teamsCompare'    => [],
            'categoryMix'     => [],
            'members'         => [],
            'topPerformers'   => [],
            'atRisk'          => [],
            'avgScoreHistory' => [],
            'smartInsights'   => [],
            'targetActive'    => 150,
            'teamsList'       => $teams,
            'peopleList'      => $people,
        ];
    }
}