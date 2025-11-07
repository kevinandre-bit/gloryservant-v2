<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberReportController extends Controller
{
    /**
     * Individual member report
     */
    public function show(int $id, Request $request)
    {
        // ---------------- Filters ----------------
        $rangeKey = strtoupper($request->input('range', 'LAST_30')); // LAST_7, LAST_14, LAST_30, LAST_90, THIS_YEAR
        [$start, $end] = $this->resolveRange($rangeKey);

        // ---------------- Person & lists ----------------
        $person = DB::table('tbl_report_people as p')
            ->leftJoin('tbl_report_teams as t', 't.id', '=', 'p.team_id')
            ->select('p.id','p.first_name','p.last_name','p.team_id','p.status','t.name as team_name')
            ->where('p.id', $id)
            ->first();

        if (!$person) {
            abort(404, 'Person not found.');
        }

        $teams = DB::table('tbl_report_teams')
            ->where('active', 1)
            ->orderBy('name')
            ->get(['id','name']);

        $people = DB::table('tbl_report_people')
            ->orderBy('last_name')->orderBy('first_name')
            ->get(['id','first_name','last_name','team_id']);

        // ---------------- Metrics & categories ----------------
        $metrics = DB::table('tbl_report_metrics as m')
            ->leftJoin('tbl_report_categories as c','c.id','=','m.category_id')
            ->where('m.active',1)
            ->orderBy('m.name')
            ->get([
                'm.id','m.name','m.value_mode','m.status_set_id','m.weight',
                DB::raw('COALESCE(c.name,"Uncategorized") as category_name'),
            ]);

        // category buckets
        $attMetricIds  = $metrics->filter(fn($m)=>stripos($m->category_name,'Attendance')===0)->pluck('id')->values()->all();
        $prodMetricIds = $metrics->filter(fn($m)=>stripos($m->category_name,'Productivity')===0)->pluck('id')->values()->all();

        // ---------------- Events (period, this person) ----------------
        $events = DB::table('tbl_report_metric_events as e')
            ->join('tbl_report_metrics as m','m.id','=','e.metric_id')
            ->leftJoin('tbl_report_status_set_items as ssi', function($j){
                $j->on('ssi.status_set_id','=','m.status_set_id')
                  // collation-safe equality
                  ->whereRaw('ssi.code COLLATE utf8mb4_unicode_ci = e.status COLLATE utf8mb4_unicode_ci');
            })
            ->where('e.person_id', $id)
            ->whereBetween('e.occurred_on', [$start, $end])
            ->orderBy('e.occurred_on','asc')
            ->get([
                'e.metric_id','e.occurred_on','e.status','e.numeric_value',
                'm.value_mode','m.status_set_id',
                DB::raw('COALESCE(ssi.score, NULL) as status_score'),
            ]);

        // normalize to score_value (0-100)
        foreach ($events as $ev) {
            $ev->score_value = ($ev->value_mode === 'status_set')
                ? (is_null($ev->status_score) ? null : (float)$ev->status_score)
                : (is_null($ev->numeric_value) ? null : (float)$ev->numeric_value);
        }

        // ---------------- KPIs ----------------
        $attVals  = collect($events)->whereIn('metric_id', $attMetricIds)->pluck('score_value')->filter(fn($v)=>$v!==null);
        $prodVals = collect($events)->whereIn('metric_id', $prodMetricIds)->pluck('score_value')->filter(fn($v)=>$v!==null);
        $anyVals  = collect($events)->pluck('score_value')->filter(fn($v)=>$v!==null);

        $kpiAttendance   = $attVals->count()  ? round($attVals->avg(), 1)  : null;
        $kpiProductivity = $prodVals->count() ? round($prodVals->avg(), 1) : null;
        $kpiComposite    = $anyVals->count()  ? round($anyVals->avg(), 1)  : null;

        // ---------------- Trend (last 12 ISO weeks) ----------------
        [$labels, $attTrend, $prodTrend] = $this->buildTrends($id, $attMetricIds, $prodMetricIds, $end);

        // ---------------- Radar by category (period) ----------------
        $byCat = DB::table('tbl_report_metric_events as e')
            ->join('tbl_report_metrics as m','m.id','=','e.metric_id')
            ->leftJoin('tbl_report_categories as c','c.id','=','m.category_id')
            ->leftJoin('tbl_report_status_set_items as ssi', function($j){
                $j->on('ssi.status_set_id','=','m.status_set_id')
                  ->whereRaw('ssi.code COLLATE utf8mb4_unicode_ci = e.status COLLATE utf8mb4_unicode_ci');
            })
            ->where('e.person_id', $id)
            ->whereBetween('e.occurred_on', [$start, $end])
            ->groupBy('c.name')
            ->get([
                DB::raw('COALESCE(c.name,"Uncategorized") as category'),
                DB::raw('AVG(COALESCE(ssi.score, e.numeric_value)) as avg_score')
            ]);

        $radarLabels = $byCat->pluck('category')->values()->all();
        $radarScores = $byCat->pluck('avg_score')->map(fn($v)=>$v? round($v,1):0)->values()->all();

        // ---------------- Recent (last 20) ----------------
        $recent = DB::table('tbl_report_metric_events as e')
            ->join('tbl_report_metrics as m','m.id','=','e.metric_id')
            ->leftJoin('tbl_report_status_set_items as ssi', function($j){
                $j->on('ssi.status_set_id','=','m.status_set_id')
                  ->whereRaw('ssi.code COLLATE utf8mb4_unicode_ci = e.status COLLATE utf8mb4_unicode_ci');
            })
            ->where('e.person_id', $id)
            ->whereBetween('e.occurred_on', [$start, $end])
            ->orderBy('e.occurred_on','desc')
            ->limit(20)
            ->get([
                'e.occurred_on','m.name as metric','e.status','e.numeric_value','m.value_mode',
                DB::raw('COALESCE(ssi.score,NULL) as status_score')
            ])
            ->map(function($r){
                $value = ($r->value_mode==='status_set')
                    ? (($r->status ?? '—').' ('.((string)$r->status_score ?? '—').')')
                    : (is_null($r->numeric_value) ? '—' : (string)$r->numeric_value);
                return [
                    'date'   => $r->occurred_on,
                    'metric' => $r->metric,
                    'value'  => $value,
                ];
            })->values()->all();

        // ---------------- Category Tabs (metrics & their rows) ----------------
        // metrics -> rows within range for this person
        $metricsByCat = [];
        foreach ($metrics as $m) {
            $metricsByCat[$m->category_name]['metrics'][] = [
                'id'   => $m->id,
                'name' => $m->name,
            ];
        }
        // attach rows per metric
        $rowsByMetric = DB::table('tbl_report_metric_events as e')
            ->join('tbl_report_metrics as m','m.id','=','e.metric_id')
            ->leftJoin('tbl_report_status_set_items as ssi', function($j){
                $j->on('ssi.status_set_id','=','m.status_set_id')
                  ->whereRaw('ssi.code COLLATE utf8mb4_unicode_ci = e.status COLLATE utf8mb4_unicode_ci');
            })
            ->leftJoin('tbl_report_categories as c','c.id','=','m.category_id')
            ->where('e.person_id', $id)
            ->whereBetween('e.occurred_on', [$start,$end])
            ->orderBy('e.occurred_on','desc')
            ->get([
                'e.occurred_on','e.metric_id','m.name as metric_name',
                'm.value_mode','e.status','e.numeric_value',
                DB::raw('COALESCE(ssi.score, e.numeric_value) as score_value'),
                DB::raw('COALESCE(c.name,"Uncategorized") as category_name'),
            ]);

        foreach ($rowsByMetric as $r) {
            $cat = $r->category_name;
            $mid = $r->metric_id;
            $metricsByCat[$cat]['rows'][$mid][] = [
                'date'   => $r->occurred_on,
                'metric' => $r->metric_name,
                'value'  => ($r->value_mode==='status_set'
                            ? (($r->status ?? '—').' ('.($r->score_value !== null ? (int)$r->score_value : '—').')')
                            : (is_null($r->numeric_value) ? '—' : (string)$r->numeric_value)),
                'score'  => $r->score_value !== null ? round($r->score_value,1) : null,
            ];
        }

        // ---------------- ViewModel ----------------
        $vm = [
            'filters' => [
                'range_key' => $rangeKey,
                'start'     => $start,
                'end'       => $end,
            ],
            'person' => [
                'id'     => $person->id,
                'name'   => trim(($person->first_name ?? '').' '.($person->last_name ?? '')),
                'team'   => $person->team_name ?? '—',
                'team_id'=> $person->team_id,
                'status' => $person->status ?? 'active',
            ],
            'lists' => [
                'teams'  => $teams,
                'people' => $people,
            ],
            'kpis' => [
                'attendance'   => $kpiAttendance,
                'productivity' => $kpiProductivity,
                'composite'    => $kpiComposite,
            ],
            'trend' => [
                'labels'       => $labels,
                'attendance'   => $attTrend,
                'productivity' => $prodTrend,
            ],
            'radar' => [
                'labels' => $radarLabels,
                'scores' => $radarScores,
            ],
            'recent'        => $recent,
            'metricsByCat'  => $metricsByCat,
        ];

        return view('admin.reports.member', compact('vm'));
    }

    /**
     * Ranges supported:
     * LAST_7, LAST_14, LAST_30, LAST_90, THIS_YEAR
     */
    private function resolveRange(string $key): array
    {
        $today = Carbon::today();
        switch ($key) {
            case 'LAST_7':   $start = $today->copy()->subDays(6); break;
            case 'LAST_14':  $start = $today->copy()->subDays(13); break;
            case 'LAST_90':  $start = $today->copy()->subDays(89); break;
            case 'THIS_YEAR':$start = $today->copy()->startOfYear(); break;
            case 'LAST_30':
            default:         $start = $today->copy()->subDays(29); break;
        }
        $end = $today;
        return [$start->toDateString(), $end->toDateString()];
    }

    /**
     * Build last 12 ISO-week trend averages for Attendance & Productivity.
     */
    private function buildTrends(int $personId, array $attIds, array $prodIds, string $endDate): array
    {
        $end = Carbon::parse($endDate)->endOfWeek();
        $labels = []; $att = []; $prod = [];

        for ($i = 11; $i >= 0; $i--) {
            $we = $end->copy()->subWeeks($i);
            $ws = $we->copy()->startOfWeek();
            $labels[] = 'W'.$ws->format('W');

            $attRows = empty($attIds) ? collect() :
                DB::table('tbl_report_metric_events as e')
                    ->join('tbl_report_metrics as m','m.id','=','e.metric_id')
                    ->leftJoin('tbl_report_status_set_items as ssi', function($j){
                        $j->on('ssi.status_set_id','=','m.status_set_id')
                          ->whereRaw('ssi.code COLLATE utf8mb4_unicode_ci = e.status COLLATE utf8mb4_unicode_ci');
                    })
                    ->where('e.person_id', $personId)
                    ->whereIn('e.metric_id', $attIds)
                    ->whereBetween('e.occurred_on', [$ws->toDateString(), $we->toDateString()])
                    ->get(['e.numeric_value','e.status','m.value_mode', DB::raw('COALESCE(ssi.score,NULL) as status_score')]);

            $prodRows = empty($prodIds) ? collect() :
                DB::table('tbl_report_metric_events as e')
                    ->join('tbl_report_metrics as m','m.id','=','e.metric_id')
                    ->leftJoin('tbl_report_status_set_items as ssi', function($j){
                        $j->on('ssi.status_set_id','=','m.status_set_id')
                          ->whereRaw('ssi.code COLLATE utf8mb4_unicode_ci = e.status COLLATE utf8mb4_unicode_ci');
                    })
                    ->where('e.person_id', $personId)
                    ->whereIn('e.metric_id', $prodIds)
                    ->whereBetween('e.occurred_on', [$ws->toDateString(), $we->toDateString()])
                    ->get(['e.numeric_value','e.status','m.value_mode', DB::raw('COALESCE(ssi.score,NULL) as status_score')]);

            $calc = function($rows){
                $vals = [];
                foreach ($rows as $r) {
                    $v = ($r->value_mode==='status_set')
                        ? (is_null($r->status_score) ? null : (float)$r->status_score)
                        : (is_null($r->numeric_value) ? null : (float)$r->numeric_value);
                    if ($v !== null) $vals[] = $v;
                }
                return $vals ? round(array_sum($vals)/count($vals), 1) : null;
            };

            $att[]  = $calc($attRows);
            $prod[] = $calc($prodRows);
        }

        return [$labels, $att, $prod];
    }
}