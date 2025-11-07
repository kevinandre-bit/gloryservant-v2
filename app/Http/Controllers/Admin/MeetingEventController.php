<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MeetingEventController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $this->validatePayload($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('MeetingEvent validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return redirect()->route('meeting-sessions.calendar')
                ->withErrors($e->validator)
                ->withInput();
        }

        $id = DB::table('meeting_events')->insertGetId($data + [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->expectsJson()) {
            $event = DB::table('meeting_events')->where('id', $id)->first();
            return response()->json($event, 201);
        }

        return redirect()->route('meeting-sessions.calendar')->with('success', 'Meeting session scheduled successfully.');
    }

    public function update(Request $request, int $event)
    {
        $data = $this->validatePayload($request, $event);

        DB::table('meeting_events')
            ->where('id', $event)
            ->update($data + ['updated_at' => now(), 'updated_by' => auth()->id()]);

        if ($request->expectsJson()) {
            $eventRow = DB::table('meeting_events')->where('id', $event)->first();
            return response()->json($eventRow, 200);
        }

        return back()->with('success', 'Meeting session updated successfully.');
    }

    public function destroy(Request $request, int $event)
    {
        DB::table('meeting_events')->where('id', $event)->delete();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('success', 'Meeting session deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'event_ids' => ['required', 'array'],
            'event_ids.*' => ['integer', 'exists:meeting_events,id'],
        ]);

        DB::table('meeting_events')->whereIn('id', $request->input('event_ids'))->delete();

        return back()->with('success', 'Selected meeting sessions deleted successfully.');
    }

    public function show(int $event)
    {
        $row = DB::table('meeting_events')->where('id', $event)->first();
        if (!$row) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json($row);
    }

    /**
     * Display a listing of meeting events for the admin UI.
     */
    public function index(Request $request)
    {
        // Redirect to the calendar view, which is now the primary interface.
        return redirect()->route('meeting-sessions.calendar');
    }

    /**
     * Simple calendar-like view for meeting events.
     */
    public function calendar()
    {
        $events = DB::table('meeting_events as e')
            ->leftJoin('meeting_link as l', 'l.id', '=', 'e.meeting_link_id')
            ->select('e.*', 'l.title as meeting_title', 'l.token as meeting_token')
            ->orderBy('e.meeting_date')
            ->orderBy('e.start_time')
            ->get()
            ->map(function ($row) {
                $row->campus_group = $row->campus_group ? json_decode($row->campus_group, true) : [];
                $row->ministry_group = $row->ministry_group ? json_decode($row->ministry_group, true) : [];
                $row->dept_group = $row->dept_group ? json_decode($row->dept_group, true) : [];
                return $row;
            });

        $meetingLinks = DB::table('meeting_link')->select('id','title')->orderBy('title')->get();
        $campuses = DB::table('tbl_form_campus')->distinct()->pluck('campus');
        $ministries = DB::table('tbl_campus_data')->distinct()->pluck('ministry');
        $departments = DB::table('tbl_campus_data')->distinct()->pluck('department');

        $cspNonce = request()->attributes->get('cspNonce');
        return view('admin.meeting-sessions.calendar', [
            'events' => $events,
            'meetingLinks' => $meetingLinks,
            'campuses' => $campuses,
            'ministries' => $ministries,
            'departments' => $departments,
            'cspNonce' => $cspNonce,
        ]);
    }

    /**
     * Display a listing of meeting events in a list format.
     */
    public function listView(Request $request)
    {
        $query = DB::table('meeting_events as e')
            ->leftJoin('meeting_link as l', 'l.id', '=', 'e.meeting_link_id')
            ->select('e.*', 'l.title as meeting_title', 'l.token as meeting_token');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('e.title', 'like', "%{$search}%")
                    ->orWhere('e.notes', 'like', "%{$search}%")
                    ->orWhere('l.title', 'like', "%{$search}%");
            });
        }

        // Filter by meeting link
        if ($request->filled('meeting_link_id')) {
            $query->where('e.meeting_link_id', $request->input('meeting_link_id'));
        }

        // Filter by meeting type
        if ($request->filled('meeting_type')) {
            $query->where('e.meeting_type', $request->input('meeting_type'));
        }

        // Filter by date range
        if ($request->filled('date_start')) {
            $query->where('e.meeting_date', '>=', $request->input('date_start'));
        }
        if ($request->filled('date_end')) {
            $query->where('e.meeting_date', '<=', $request->input('date_end'));
        }

        $events = $query->orderByDesc('e.meeting_date')
            ->orderBy('e.start_time')
            ->get()
            ->map(function ($row) {
                // decode JSON audience groups for display convenience
                $row->campus_group = $row->campus_group ? json_decode($row->campus_group, true) : [];
                $row->ministry_group = $row->ministry_group ? json_decode($row->ministry_group, true) : [];
                $row->dept_group = $row->dept_group ? json_decode($row->dept_group, true) : [];
                return $row;
            });

        $meetingLinks = DB::table('meeting_link')->select('id','title')->orderBy('title')->get();
        $campuses = DB::table('tbl_form_campus')->distinct()->pluck('campus');
        $ministries = DB::table('tbl_campus_data')->distinct()->pluck('ministry');
        $departments = DB::table('tbl_campus_data')->distinct()->pluck('department');

        return view('admin.meeting-sessions.index', [
            'events' => $events,
            'meetingLinks' => $meetingLinks,
            'campuses' => $campuses,
            'ministries' => $ministries,
            'departments' => $departments,
        ]);
    }

    /**
     * Return JSON feed of events suitable for FullCalendar.
     */
    public function eventsJson()
    {
        $rows = DB::table('meeting_events as e')
            ->leftJoin('meeting_link as l', 'l.id', '=', 'e.meeting_link_id')
            ->select('e.*', 'l.title as meeting_title', 'l.token as meeting_token')
            ->get();

        $events = $rows->map(function ($r) {
            $date = $r->meeting_date ?: null;
            $start = null;
            $end = null;
            if ($date && $r->start_time) {
                $start = $date . 'T' . substr($r->start_time, 0, 8);
            } elseif ($date) {
                $start = $date;
            }
            if ($date && $r->end_time) {
                $end = $date . 'T' . substr($r->end_time, 0, 8);
            }

            $title = $r->meeting_title ?: ('Session #' . $r->id);
            if (!empty($r->title)) {
                $title = $r->title . ' — ' . $title;
            }

            // prepare audience summary
            $campuses = $r->campus_group ? json_decode($r->campus_group, true) : [];
            $ministries = $r->ministry_group ? json_decode($r->ministry_group, true) : [];
            $depts = $r->dept_group ? json_decode($r->dept_group, true) : [];
            $aud = [];
            if (!empty($campuses)) $aud[] = 'Campus: ' . implode(', ', $campuses);
            if (!empty($ministries)) $aud[] = 'Ministry: ' . implode(', ', $ministries);
            if (!empty($depts)) $aud[] = 'Dept: ' . implode(', ', $depts);
            $audienceText = $aud ? implode(' | ', $aud) : 'All';

            // color by meeting_type
            $color = null;
            if (isset($r->meeting_type)) {
                if ($r->meeting_type === 'training') {
                    $color = '#38A169'; // green
                } elseif ($r->meeting_type === 'meeting') {
                    $color = '#2B6CB0'; // blue
                }
            }

            return [
                'id'    => $r->id,
                'title' => $title,
                'start' => $start,
                'end'   => $end,
                'color' => $color,
                'extendedProps' => [
                    'meeting_type' => $r->meeting_type,
                    'video_url'    => $r->video_url,
                    'meeting_link_token' => $r->meeting_token,
                    'audience' => $audienceText,
                ],
                'url' => $r->video_url ?: url('attendance/' . ($r->meeting_token ?? '')),
            ];
        });

        return response()->json($events->values());
    }

    private function validatePayload(Request $request, ?int $eventId = null): array
    {
        $data = $request->validate([
            'meeting_link_id' => ['required', Rule::exists('meeting_link', 'id')],
            'title'           => ['nullable', 'string', 'max:200'],
            'meeting_date'    => ['nullable', 'date'],
            'start_time'      => ['nullable', 'date_format:H:i'],
            'end_time'        => ['nullable', 'date_format:H:i', 'after_or_equal:start_time'],
            'frequency'       => ['nullable', Rule::in(['once','weekly','biweekly','monthly','quarterly','custom'])],
            'frequency_meta'  => ['nullable'],
            'expires_at'      => ['nullable', 'date'],
            'video_url'       => ['nullable', 'url', 'max:500'],
            'meeting_type'    => ['required', Rule::in(['meeting','training'])],
            'campus_group'    => ['nullable', 'array'],
            'campus_group.*'  => ['string', 'max:120'],
            'ministry_group'  => ['nullable', 'array'],
            'ministry_group.*'=> ['string', 'max:120'],
            'dept_group'      => ['nullable', 'array'],
            'dept_group.*'    => ['string', 'max:120'],
            'notes'           => ['nullable', 'string'],
        ]);

        $payload = [
            'meeting_link_id' => (int) $data['meeting_link_id'],
            'title'           => $data['title'] ?? null,
            'meeting_date'    => !empty($data['meeting_date']) ? Carbon::parse($data['meeting_date'])->toDateString() : null,
            'start_time'      => !empty($data['start_time']) ? Carbon::createFromFormat('H:i', $data['start_time'])->format('H:i:00') : null,
            'end_time'        => !empty($data['end_time']) ? Carbon::createFromFormat('H:i', $data['end_time'])->format('H:i:00') : null,
            'frequency'       => $data['frequency'] ?? 'once',
            'frequency_meta'  => $this->prepareFrequencyMeta($data['frequency_meta'] ?? null),
            'expires_at'      => !empty($data['expires_at']) ? Carbon::parse($data['expires_at'])->toDateTimeString() : null,
            'video_url'       => $data['video_url'] ?? null,
            'meeting_type'    => $data['meeting_type'],
            'campus_group'    => isset($data['campus_group']) ? json_encode(array_values(array_unique($data['campus_group']))) : null,
            'ministry_group'  => isset($data['ministry_group']) ? json_encode(array_values(array_unique($data['ministry_group']))) : null,
            'dept_group'      => isset($data['dept_group']) ? json_encode(array_values(array_unique($data['dept_group']))) : null,
            'notes'           => $data['notes'] ?? null,
        ];

        $userId = auth()->id();
        if (!$eventId) {
            $payload['created_by'] = $userId;
        } else {
            $payload['updated_by'] = $userId;
        }

        return $payload;
    }

    private function prepareFrequencyMeta($meta): ?string
    {
        if ($meta === null || $meta === '') {
            return null;
        }

        if (is_string($meta)) {
            $meta = ['note' => $meta];
        }

        if (is_array($meta)) {
            return json_encode($meta);
        }

        return null;
    }

    /**
     * Improved meeting sessions interface
     */
    public function indexImproved()
    {
        $meetingLinks = DB::table('meeting_link')->select('id','title')->get();
        $campuses = collect(['Campus A', 'Campus B']);
        $ministries = collect(['Ministry 1', 'Ministry 2']);
        $departments = collect(['Dept 1', 'Dept 2']);

        return view('admin.meeting-sessions.index', [
            'meetingLinks' => $meetingLinks,
            'campuses' => $campuses,
            'ministries' => $ministries,
            'departments' => $departments,
            'cspNonce' => app('csp_nonce'),
        ]);
    }
}
