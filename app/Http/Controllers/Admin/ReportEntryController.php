<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportEntryController extends Controller
{
    /* --------------------------------------------------------
     |  Landing (no team selected)
     * -------------------------------------------------------- */
    public function index()
    {
        return view('admin.reports.entry', [
            'teams'       => DB::table('tbl_report_teams')->where('active',1)->orderBy('name')->get(),
            'team'        => null,
            'members'     => collect(),
            'assignments' => [],          // always present for the Blade
            'statusSets'  => collect(),   // always present for the Blade
        ]);
    }

    /* --------------------------------------------------------
     |  Team page (loads people, assignments, status sets)
     * -------------------------------------------------------- */
    public function teamEntry($teamId)
    {
        $team = DB::table('tbl_report_teams')->find($teamId);
        abort_if(!$team, 404);

        $teams = DB::table('tbl_report_teams')->where('active',1)->orderBy('name')->get();

        $members = DB::table('tbl_report_people')
            ->where('team_id', $teamId)
            ->where(function($q){ $q->whereNull('status')->orWhere('status','active'); })
            ->orderBy('last_name')->orderBy('first_name')
            ->get();

        $memberIds = $members->pluck('id')->all();

        // Assigned metrics for these people (active window)
        $rows = DB::table('tbl_report_assignments as a')
    ->join('tbl_report_metrics as m', 'm.id', '=', 'a.metric_id')
    ->leftJoin('tbl_report_categories as c', 'c.id', '=', 'm.category_id')
    ->whereIn('a.person_id', $memberIds)
    ->where(function ($q) {
        $today = now()->toDateString();
        $q->whereNull('a.ends_on')->orWhere('a.ends_on', '>=', $today);
    })
    ->orderBy('m.name')
    ->get([
        'a.person_id',
        'm.id   as metric_id',
        'm.name as metric_name',
        'm.value_mode',         // <-- important
        'm.status_set_id',      // <-- important (nullable)
        'c.name as category_name',
    ]);

$assignments = [];
foreach ($rows as $r) {
    $pid = (string)$r->person_id;
    $assignments[$pid][] = [
        'metric_id'     => (int)$r->metric_id,
        'metric_name'   => $r->metric_name,
        'category_name' => $r->category_name,
        'value_mode'    => $r->value_mode,           // 'status_set' or 'scale100'
        'status_set_id' => $r->status_set_id,        // may be null
    ];
}

        // Load status sets + options once
        // -> { set_id : [ {value,label,score}, ... ] }
        $statusSets     = DB::table('tbl_report_status_sets')->where('active',1)->get(['id','name']);
$statusSetItems = DB::table('tbl_report_status_set_items')
    ->where('active',1)
    ->orderBy('sort_order')
    ->get(['id','status_set_id','code','label','score']);

        return view('admin.reports.entry', [
            'team'        => $team,
            'teams'       => $teams,
            'members'     => $members,
            'assignments'       => $assignments,
    'statusSets'        => $statusSets,
    'statusSetItems'    => $statusSetItems,
        ]);
    }

    /* --------------------------------------------------------
     |  Save / Upsert daily entries (bulk from modal or single)
     * -------------------------------------------------------- */
    public function storeEvent(Request $req)
    {
        $isBulk = (bool) $req->boolean('bulk');

        $base = $req->validate([
            'person_id'   => 'required|exists:tbl_report_people,id',
            'occurred_on' => 'required|date',
            'source'      => 'nullable|string|max:80',
            'note'        => 'nullable|string',
        ]);

        $personId   = (int)$base['person_id'];
        $occurredOn = $base['occurred_on'];
        $source     = $base['source'] ?? 'manual';
        $userIdno   = auth()->user()->idno ?? 'SYS';
        $now        = now();

        // Preload metric definitions for validation
        $metricIds = [];
        if ($isBulk) {
            foreach ((array)$req->input('metrics', []) as $row) {
                if (!empty($row['metric_id'])) $metricIds[] = (int)$row['metric_id'];
            }
        } else {
            $metricIds[] = (int)$req->input('metric_id');
        }
        $metricIds = array_values(array_unique(array_filter($metricIds)));

        $metricsMap = [];
        if ($metricIds) {
            $defs = DB::table('tbl_report_metrics')->whereIn('id', $metricIds)->get([
                'id','value_mode','status_set_id'
            ]);
            foreach ($defs as $d) {
                $metricsMap[(int)$d->id] = [
                    'value_mode'    => $d->value_mode,
                    'status_set_id' => $d->status_set_id,
                ];
            }
        }

        // Preload allowed status values for any involved sets
        // Preload allowed status values for any involved sets
		$setIds = collect($metricsMap)->pluck('status_set_id')->filter()->unique()->values()->all();
		$allowed = []; // status_set_id => [code,...]
		if ($setIds) {
		    $rows = DB::table('tbl_report_status_set_items')
		        ->whereIn('status_set_id', $setIds)
		        ->where('active', 1)
		        ->get(['status_set_id','code']);

		    foreach ($rows as $r) {
		        $allowed[(int)$r->status_set_id][] = (string)$r->code;
		    }
		}

        // Build rows to upsert
        $payloadNote = $base['note'] ?? null;

        $items = [];
        if ($isBulk) {
            foreach ((array)$req->input('metrics', []) as $row) {
                $mid    = isset($row['metric_id']) ? (int)$row['metric_id'] : 0;
                $status = isset($row['status']) ? trim((string)$row['status']) : null;
                $num    = $row['numeric_value'] ?? null;

                if (!$mid) continue;
                if (($status === null || $status === '') && ($num === null || $num === '')) continue;

                $this->validatePerMetric($mid, $metricsMap, $allowed, $status, $num);

                $items[] = [
                    'person_id'     => $personId,
                    'metric_id'     => $mid,
                    'occurred_on'   => $occurredOn,
                    'status'        => $status !== '' ? $status : null,
                    'numeric_value' => ($num !== '' && $num !== null) ? (int)round($num) : null,
                    'source'        => $source,
                    'payload'       => $payloadNote ? json_encode(['note'=>$payloadNote]) : null,
                    'created_by'    => $userIdno,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }
        } else {
            $data = $req->validate([
                'metric_id'     => 'required|exists:tbl_report_metrics,id',
                'status'        => 'nullable|string|max:120',
                'numeric_value' => 'nullable|numeric|min:0|max:100',
            ]);
            $mid = (int)$data['metric_id'];
            $this->validatePerMetric($mid, $metricsMap, $allowed, $data['status'] ?? null, $data['numeric_value'] ?? null);

            $items[] = [
                'person_id'     => $personId,
                'metric_id'     => $mid,
                'occurred_on'   => $occurredOn,
                'status'        => $data['status'] ?? null,
                'numeric_value' => isset($data['numeric_value']) ? (int)$data['numeric_value'] : null,
                'source'        => $source,
                'payload'       => $payloadNote ? json_encode(['note'=>$payloadNote]) : null,
                'created_by'    => $userIdno,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        if (!$items) {
            return back()->with('error', 'No items to save.');
        }

        // Per-day upsert: (person_id, metric_id, occurred_on)
        foreach ($items as $row) {
            DB::table('tbl_report_metric_events')->updateOrInsert(
                [
                    'person_id'   => $row['person_id'],
                    'metric_id'   => $row['metric_id'],
                    'occurred_on' => $row['occurred_on'],
                ],
                [
                    'status'        => $row['status'],
                    'numeric_value' => $row['numeric_value'],
                    'source'        => $row['source'],
                    'payload'       => $row['payload'],
                    'created_by'    => $row['created_by'],
                    'updated_at'    => $now,
                ]
            );
        }

        return back()->with('success', 'Saved '.count($items).' item(s).');
    }

    // GET /admin/reports/entry/events/day?person_id=123&date=2025-09-17
public function dayEvents(Request $req)
{
    $data = $req->validate([
        'person_id' => 'required|integer|exists:tbl_report_people,id',
        'date'      => 'required|date',
    ]);

    $rows = DB::table('tbl_report_metric_events')
        ->where('person_id', $data['person_id'])
        ->where('occurred_on', $data['date'])
        ->get(['metric_id','status','numeric_value']);

    // Return as { metric_id: {status, numeric_value} }
    $out = [];
    foreach ($rows as $r) {
        $out[(int)$r->metric_id] = [
            'status'        => $r->status,
            'numeric_value' => $r->numeric_value,
        ];
    }

    return response()->json($out);
}

    /* --------------------------------------------------------
     |  Validation per metric (mode-aware)
     * -------------------------------------------------------- */
    private function validatePerMetric(int $metricId, array $metricsMap, array $allowed, ?string $status, $num): void
    {
        $def = $metricsMap[$metricId] ?? null;
        if (!$def) return; // unknown; let DB constraints handle

        $mode    = strtolower((string)$def['value_mode']);
        $setId   = $def['status_set_id'];

        if ($mode === 'scale') {
            // Pure 0–100 required
            if ($num === null || $num === '' || !is_numeric($num)) {
                $this->fail('numeric_value', 'This metric requires a 0–100 score.');
            }
            $numF = (float)$num;
            if ($numF < 0 || $numF > 100) {
                $this->fail('numeric_value', 'Score must be between 0 and 100.');
            }
            // Status is ignored for scale
            return;
        }

        if ($mode === 'status_set') {
            if ($setId && isset($allowed[$setId])) {
                if ($status !== null && $status !== '') {
                    if (!in_array($status, $allowed[$setId], true)) {
                        $this->fail('status', 'Invalid selection for this metric.');
                    }
                }
            }
            // Numeric is optional but must be 0–100 if provided
            if ($num !== null && $num !== '') {
                if (!is_numeric($num)) $this->fail('numeric_value', 'Score must be numeric.');
                $numF = (float)$num;
                if ($numF < 0 || $numF > 100) {
                    $this->fail('numeric_value', 'Score must be between 0 and 100.');
                }
            }
            return;
        }

        // Fallback: allow both empty or 0–100
        if ($num !== null && $num !== '') {
            if (!is_numeric($num)) $this->fail('numeric_value', 'Score must be numeric.');
            $numF = (float)$num;
            if ($numF < 0 || $numF > 100) $this->fail('numeric_value', 'Score must be between 0 and 100.');
        }
    }

    private function fail(string $field, string $msg): void
    {
        throw \Illuminate\Validation\ValidationException::withMessages([$field => $msg]);
    }
}