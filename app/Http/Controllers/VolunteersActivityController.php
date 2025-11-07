<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class VolunteersActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request, $id)
    {
        // Validate filters
        $request->validate([
            'from' => ['nullable','date'],
            'to'   => ['nullable','date','after_or_equal:from'],
            'type' => ['nullable','in:all,clock,devotion,meeting'],
            'q'    => ['nullable','string','max:200'],
        ]);

        // Read filters
        $from = $request->date('from'); // Carbon|null
        $to   = $request->date('to');   // Carbon|null
        $type = $request->input('type', 'all'); // all|clock|devotion|meeting
        $term = $request->filled('q') ? '%'.$request->q.'%' : null;

        // Volunteer header info
        $select = ['p.id','p.firstname','p.mi','p.lastname'];
        if (Schema::hasTable('tbl_campus_data') && Schema::hasColumn('tbl_campus_data','campus')) {
            $select[] = 'cd.campus';
        }
        if (Schema::hasColumn('tbl_people','emailaddress')) {
            $select[] = DB::raw('p.emailaddress as email');
        } elseif (Schema::hasColumn('tbl_people','email')) {
            $select[] = DB::raw('p.email as email');
        }

        $vol = DB::table('tbl_people as p')
            ->leftJoin('tbl_campus_data as cd','cd.reference','=','p.id')
            ->select($select)
            ->where('p.id',$id)
            ->first();

        if (!$vol) {
            return redirect()->route('volunteers.index')->with('error','Volunteer not found.');
        }

        // If meetings exist, we match via users.reference = tbl_people.id
        $userId = DB::table('users')->where('reference', $id)->value('id');

        $activities = collect();
        $debug = ['clock'=>0,'devotion'=>0,'meeting'=>0];

        // =============== CLOCK (tbl_people_attendance) ===============
        if (Schema::hasTable('tbl_people_attendance')) {
            $tbl = 'tbl_people_attendance';
            $q = DB::table($tbl)->where('reference',$id);

            if ($from) $q->whereDate('date','>=',$from);
            if ($to)   $q->whereDate('date','<=',$to);

            if ($term) {
                $q->where(function($w) use ($tbl,$term){
                    foreach (['reason','comment','status_timein','status_timeout','employee','idno'] as $c) {
                        if (Schema::hasColumn($tbl,$c)) $w->orWhere($c,'like',$term);
                    }
                });
            }

            $rows = $q->select([
                'id','date',
                Schema::hasColumn($tbl,'timein')         ? 'timein'         : DB::raw("NULL as timein"),
                Schema::hasColumn($tbl,'timeout')        ? 'timeout'        : DB::raw("NULL as timeout"),
                Schema::hasColumn($tbl,'status_timein')  ? 'status_timein'  : DB::raw("NULL as status_timein"),
                Schema::hasColumn($tbl,'status_timeout') ? 'status_timeout' : DB::raw("NULL as status_timeout"),
                Schema::hasColumn($tbl,'reason')         ? 'reason'         : DB::raw("NULL as reason"),
                Schema::hasColumn($tbl,'comment')        ? 'comment'        : DB::raw("NULL as comment"),
            ])->orderByDesc('date')->orderByDesc('id')->get();

            foreach ($rows as $r) {
                $start = $this->normalizeDateTime($r->date ?? '', $r->timein ?? '');
                $end   = $this->normalizeDateTime($r->date ?? '', $r->timeout ?? '');
                $duration = $this->shortDuration($start, $end); // "2h 18m" or null

                $bits = [];
                if ($start) {
                    $bits[] = 'IN '.Carbon::parse($start)->format('g:i A')
                           . (!empty($r->status_timein) ? ' ('.$r->status_timein.')' : '');
                }
                if ($end) {
                    $bits[] = 'OUT '.Carbon::parse($end)->format('g:i A')
                           . (!empty($r->status_timeout) ? ' ('.$r->status_timeout.')' : '');
                }
                $title = implode(' → ', $bits);
                if ($duration) $title .= ' • '.$duration;

                $happenedAt = $end ?: ($start ?: ($r->date ? $r->date.' 00:00:00' : null));

                $activities->push((object)[
                    'kind'        => 'clock',
                    'happened_at' => $happenedAt,
                    'title'       => $title ?: 'Clock',
                    'details'     => $r->reason ?? $r->comment ?? null,
                ]);
                $debug['clock']++;
            }
        }

        // =============== DEVOTIONS (tbl_people_devotion) ===============
        if (Schema::hasTable('tbl_people_devotion')) {
            $tbl = 'tbl_people_devotion';
            $dateCol = Schema::hasColumn($tbl,'devotion_date') ? 'devotion_date' : 'created_at';

            $q = DB::table($tbl)->where('reference',$id);
            if ($from) $q->whereDate($dateCol,'>=',$from);
            if ($to)   $q->whereDate($dateCol,'<=',$to);

            if ($term) {
                $q->where(function($w) use ($tbl,$term){
                    foreach (['devotion_text','comment','status'] as $c) {
                        if (Schema::hasColumn($tbl,$c)) $w->orWhere($c,'like',$term);
                    }
                });
            }

            $rows = $q->orderByDesc($dateCol)->orderByDesc('id')->get();
            foreach ($rows as $r) {
                $status  = property_exists($r,'status') ? (string)$r->status : null;
                $title   = 'Devotion'.($status ? " ({$status})" : '');
                $details = property_exists($r,'devotion_text') ? (string)$r->devotion_text
                          : (property_exists($r,'comment') ? (string)$r->comment : null);

                $activities->push((object)[
                    'kind'        => 'devotion',
                    'happened_at' => $r->{$dateCol} ?? null,
                    'title'       => $title,
                    'details'     => $details,
                ]);
                $debug['devotion']++;
            }
        }

        // =============== MEETINGS (meeting_attendance) ===============
        if ($userId && Schema::hasTable('meeting_attendance')) {
            $tbl = 'meeting_attendance';
            $dateCol = Schema::hasColumn($tbl,'meeting_date') ? 'meeting_date' : 'created_at';

            $q = DB::table($tbl)->where('user_id',$userId);
            if ($from) $q->whereDate($dateCol,'>=',$from);
            if ($to)   $q->whereDate($dateCol,'<=',$to);

            if ($term) {
                $q->where(function($w) use ($tbl,$term){
                    foreach (['Meeting','meeting_code','meeting_type'] as $c) {
                        if (Schema::hasColumn($tbl,$c)) $w->orWhere($c,'like',$term);
                    }
                });
            }

            $rows = $q->orderByDesc($dateCol)->orderByDesc('id')->get();
            foreach ($rows as $r) {
                $name = property_exists($r,'Meeting') ? (string)$r->Meeting : 'Meeting';
                $typeCol = property_exists($r,'meeting_type') ? (string)$r->meeting_type : null;
                $code = property_exists($r,'meeting_code') ? (string)$r->meeting_code : null;

                $title = $name;
                if ($typeCol) $title .= " ({$typeCol})";
                if ($code)    $title .= " [{$code}]";

                $activities->push((object)[
                    'kind'        => 'meeting',
                    'happened_at' => $r->{$dateCol} ?? null,
                    'title'       => $title,
                    'details'     => null,
                ]);
                $debug['meeting']++;
            }
        }

        // Filter by type, then sort & paginate
        if ($type !== 'all') {
            $activities = $activities->where('kind', $type);
        }

        $sorted  = $activities->sortByDesc(fn($a) => $a->happened_at)->values();
        $perPage = 50;
        $page    = max((int)$request->input('page',1),1);
        $total   = $sorted->count();
        $slice   = $sorted->slice(($page-1)*$perPage, $perPage)->values();

        $paginator = (object)[
            'items'    => $slice,
            'total'    => $total,
            'perPage'  => $perPage,
            'current'  => $page,
            'lastPage' => (int) ceil($total / $perPage),
        ];

        return view('volunteers.activity', [
            'vol'     => $vol,
            'p'       => $paginator,
            'filters' => [
                'from' => $from ? $from->toDateString() : '',
                'to'   => $to   ? $to->toDateString()   : '',
                'type' => $type,
                'q'    => $request->q ?? '',
            ],
            'debug'   => $debug, // optional banner with ?debug=1
        ]);
    }

    /** Normalize (date, time) to 'Y-m-d H:i:s', including odd "YYYY-mm-dd YYYY-mm-dd hh:mm:ss AM" cases */
    private function normalizeDateTime($date, $time): ?string
    {
        $date = trim((string)$date);
        $time = trim((string)$time);

        if ($time === '' || $time === '0000-00-00 00:00:00' || $time === '00:00:00') return null;

        // If $time already includes a date, parse it
        if (preg_match('/\d{4}-\d{2}-\d{2}/', $time)) {
            try { return Carbon::parse($time)->format('Y-m-d H:i:s'); }
            catch (\Throwable $e) {
                // Handle duplicated date like "2025-07-14 2025-07-14 09:41:00 PM"
                if (preg_match('/^(\d{4}-\d{2}-\d{2})\s+\1\s+(.+)$/', $time, $m)) {
                    try { return Carbon::parse($m[1].' '.$m[2])->format('Y-m-d H:i:s'); } catch (\Throwable $e2) {}
                }
            }
        }

        $candidate = trim($date.' '.$time);
        try { return Carbon::parse($candidate)->format('Y-m-d H:i:s'); }
        catch (\Throwable $e) {
            foreach (['Y-m-d h:i:s A','Y-m-d H:i:s','Y-m-d h:i A','Y-m-d H:i','m/d/Y h:i:s A','m/d/Y H:i:s','d/m/Y H:i:s','d/m/Y h:i:s A'] as $fmt) {
                try { return Carbon::createFromFormat($fmt, $candidate)->format('Y-m-d H:i:s'); } catch (\Throwable $e2) {}
            }
        }
        return null;
    }

    /** Return short duration like "2h 18m" or null */
    private function shortDuration(?string $start, ?string $end): ?string
    {
        if (!$start || !$end) return null;
        try {
            $s = Carbon::parse($start);
            $e = Carbon::parse($end);
            if ($e->lessThan($s)) return null;
            $diff = $e->diff($s);
            $h = $diff->h + ($diff->d * 24);
            $m = $diff->i;
            $parts = [];
            if ($h) $parts[] = $h.'h';
            if ($m) $parts[] = $m.'m';
            if (!$parts) $parts[] = $diff->s.'s';
            return implode(' ', $parts);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
