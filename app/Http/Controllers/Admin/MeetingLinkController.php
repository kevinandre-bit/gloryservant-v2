<?php
// app/Http/Controllers/Admin/MeetingLinkController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Cache;
use App\Models\MeetingAttendance;
use Illuminate\Validation\Rule;

Cache::forget('nav:meetings-links');
Cache::forget('nav:lists'); // only if you changed lists
class MeetingLinkController extends Controller
{
    /**
     * List meeting links + provide lists for modal (campus/ministry/dept)
     */
    public function index()
    {
        $now = Carbon::now();

        $meetingRecords = DB::table('meeting_link')
            ->leftJoin('tbl_meeting_category as cat', 'cat.id', '=', 'meeting_link.category_id')
            ->select('meeting_link.*', 'cat.category as category_name')
            ->orderByDesc('meeting_link.id')
            ->get();

        $linkIds = $meetingRecords->pluck('id')->all();

        $eventsByLink = collect();
        if (!empty($linkIds)) {
            $eventsByLink = DB::table('meeting_events')
                ->whereIn('meeting_link_id', $linkIds)
                ->orderBy('meeting_date')
                ->orderBy('start_time')
                ->get()
                ->map(function ($event) {
                    $event->meeting_type = $event->meeting_type ?? 'meeting';
                    $event->meeting_date_carbon = $event->meeting_date
                        ? Carbon::parse($event->meeting_date)
                        : null;
                    $event->campus_group = $event->campus_group
                        ? json_decode($event->campus_group, true)
                        : [];
                    $event->ministry_group = $event->ministry_group
                        ? json_decode($event->ministry_group, true)
                        : [];
                    $event->dept_group = $event->dept_group
                        ? json_decode($event->dept_group, true)
                        : [];
                    $event->frequency_meta = $event->frequency_meta
                        ? json_decode($event->frequency_meta, true)
                        : null;

                    return $event;
                })
                ->groupBy('meeting_link_id');
        }

        $meetings = $meetingRecords->map(function ($m) use ($now, $eventsByLink) {
            $m->title         = (string) ($m->title ?? 'Untitled link');
            $m->description   = $m->description !== null ? (string) $m->description : null;
            $m->mode          = (string) ($m->mode ?? 'auto');
            $m->token         = (string) $m->token;
            $m->category_id   = $m->category_id ?? null;
            $m->category_name = $m->category_name ? (string) $m->category_name : null;

            $m->url    = url("/meeting-attendance/{$m->token}");
            $m->qr_url = $m->qr_path ? '/'.ltrim($m->qr_path, '/') : null;
            $m->require_auth = (bool) ($m->require_auth ?? 1);

            $expiresAt = $m->expires_at ? Carbon::parse($m->expires_at) : null;
            $m->expires_at_carbon = $expiresAt;
            $m->is_expired = $expiresAt ? $expiresAt->lt($now) : false;
            $m->expires_label = $expiresAt ? $expiresAt->format('M d, Y') : 'No expiry';
            $m->expires_badge = $m->is_expired
                ? 'Expired'
                : ($expiresAt ? 'Scheduled' : 'Active');
            $m->created_label = $m->created_at ? Carbon::parse($m->created_at)->format('M d, Y') : '—';

            $events = $eventsByLink->get($m->id, collect());
            $m->events = $events;
            $m->events_count = $events->count();

            $futureEvents = $events->filter(function ($event) use ($now) {
                return $event->meeting_date_carbon
                    ? $event->meeting_date_carbon->gte($now->copy()->startOfDay())
                    : false;
            })->sortBy(function ($event) {
                return $event->meeting_date_carbon ? $event->meeting_date_carbon->timestamp : PHP_INT_MAX;
            })->values();

            $m->upcoming_events = $futureEvents;
            $m->next_event = $futureEvents->first();

            $flatten = function ($events, $key) {
                return $events->flatMap(function ($event) use ($key) {
                    $values = $event->{$key} ?? [];
                    return collect($values)->filter();
                })->unique()->values()->all();
            };

            $m->campus_group   = $flatten($events, 'campus_group');
            $m->ministry_group = $flatten($events, 'ministry_group');
            $m->dept_group     = $flatten($events, 'dept_group');

            return $m;
        });

        $categories = DB::table('tbl_meeting_category')
            ->select('id', 'category', 'created_at')
            ->orderBy('category')
            ->get()
            ->map(function ($row) {
                $row->category = (string) $row->category;
                return $row;
            });

        $stats = [
            'total'        => $meetings->count(),
            'active'       => $meetings->where('is_expired', false)->count(),
            'expired'      => $meetings->where('is_expired', true)->count(),
            'auto_count'   => $meetings->where('mode', 'auto')->count(),
            'form_count'   => $meetings->where('mode', 'form')->count(),
            'expiring_soon'=> $meetings->filter(function ($m) use ($now) {
                return $m->expires_at_carbon
                    && !$m->is_expired
                    && $m->expires_at_carbon->between($now, $now->copy()->addDays(7));
            })->count(),
            'categories'   => $categories->count(),
        ];

        // Pluck lists (adjust tables if yours differ)
        $campuses = DB::table('tbl_form_campus')
            ->select('id', 'campus as label')
            ->orderBy('campus')
            ->get()
            ->map(fn ($row) => (object)[
                'id'    => $row->id,
                'label' => (string) ($row->label ?? ''),
            ])
            ->filter(fn ($row) => $row->label !== '')
            ->values();

        $ministries = DB::table('tbl_form_ministry')
            ->select('id', 'ministry as label')
            ->orderBy('ministry')
            ->get()
            ->map(fn ($row) => (object)[
                'id'    => $row->id,
                'label' => (string) ($row->label ?? ''),
            ])
            ->filter(fn ($row) => $row->label !== '')
            ->values();

        $departments = DB::table('tbl_form_department')
            ->select('id', 'department as label')
            ->orderBy('department')
            ->get()
            ->map(fn ($row) => (object)[
                'id'    => $row->id,
                'label' => (string) ($row->label ?? ''),
            ])
            ->filter(fn ($row) => $row->label !== '')
            ->values();

        $categoryLookup = $categories->pluck('category', 'id')->toArray();

        $groupedMeetings = $meetings->groupBy(function ($meeting) {
            return $meeting->category_id !== null
                ? (string) $meeting->category_id
                : 'uncategorized';
        })->sortBy(function ($collection, $key) use ($categoryLookup) {
            if ($key === 'uncategorized') {
                return 'zzz-'.$collection->count();
            }

            return strtolower($categoryLookup[$key] ?? 'zzz');
        });

        return view('admin.meeting-links.index', [
            'meetings'        => $meetings,
            'campuses'        => $campuses,
            'ministries'      => $ministries,
            'departments'     => $departments,
            'stats'           => $stats,
            'categories'      => $categories,
            'groupedMeetings' => $groupedMeetings,
            'categoryLookup'  => $categoryLookup,
        ]);
    }

    /**
     * Create a meeting link (date expiry, checkbox groups, QR generation)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:150',
            'description'  => 'nullable|string|max:5000',
            'expires_in'   => 'nullable|date',
            'category_id'  => 'nullable|exists:tbl_meeting_category,id',
            'mode'         => 'required|in:auto,form',
            'require_auth' => 'nullable|boolean',
        ]);

        // Generate unique 24-hex token
        $token = substr(bin2hex(random_bytes(16)), 0, 24);
        while (DB::table('meeting_link')->where('token', $token)->exists()) {
            $token = substr(bin2hex(random_bytes(16)), 0, 24);
        }

        $attendanceUrl = url("/meeting-attendance/{$token}");

        // Generate QR
        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(700)
            ->margin(1)
            ->generate($attendanceUrl);

        $fileName = "attendance_{$token}.svg";
        $filePath = public_path("assets2/qr/{$fileName}");
        file_put_contents($filePath, $svg);
        $qr_path = "assets2/qr/{$fileName}";

        $expiresAt = !empty($data['expires_in'])
            ? \Carbon\Carbon::parse($data['expires_in'])->endOfDay()
            : null;

        DB::table('meeting_link')->insert([
            'title'          => $data['title'],
            'description'    => $data['description'] ?? null,
            'token'          => $token,
            'expires_at'     => $expiresAt,
            'qr_path'        => $qr_path,
            'created_by'     => auth()->id(),
            'mode'           => $data['mode'],
            'require_auth'   => (int)($data['require_auth'] ?? 1),
            'category_id'    => $data['category_id'] ?? null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return back()->with('success', 'Meeting link created successfully!');
    }
    // app/Http/Controllers/Admin/MeetingLinkController.php
    public function update(Request $req, $id)
    {
        $data = $req->validate([
            'title'        => 'required|string|max:150',
            'description'  => 'nullable|string|max:5000',
            'expires_in'   => 'nullable|date',
            'category_id'  => 'nullable|exists:tbl_meeting_category,id',
            'mode'         => 'required|in:auto,form',
            'require_auth' => 'nullable|boolean',
        ]);

        $expiresAt = !empty($data['expires_in'])
            ? \Carbon\Carbon::parse($data['expires_in'])->endOfDay()
            : null;

        DB::table('meeting_link')->where('id', $id)->update([
            'title'        => $data['title'],
            'description'  => $data['description'] ?? null,
            'expires_at'   => $expiresAt,
            'mode'         => $data['mode'],
            'require_auth' => (int)($data['require_auth'] ?? 1),
            'category_id'  => $data['category_id'] ?? null,
            'updated_at'   => now(),
        ]);

        return back()->with('success', 'Meeting link updated successfully!');
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'max:150', Rule::unique('tbl_meeting_category', 'category')],
        ]);

        $timestamp = now();

        $id = DB::table('tbl_meeting_category')->insertGetId([
            'category'   => $data['category'],
            'created_by' => auth()->id(),
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        $category = DB::table('tbl_meeting_category')->where('id', $id)->first();

        if ($request->expectsJson()) {
            return response()->json($category, 201);
        }

        return back()->with('success', 'Category added successfully.');
    }

    public function attendanceView()
    {
        $attendances = MeetingAttendance::orderByDesc('meeting_date')->paginate(50);
        return view('admin.meeting_attendance', compact('attendances'));
    }

    public function destroy(int $id)
    {
        $row = DB::table('meeting_link')->where('id', $id)->first();
        if (!$row) {
            return back()->with('flash', [
                'type' => 'danger',
                'msg'  => 'Meeting link not found.',
            ]);
        }

        // Try to remove the QR file (best-effort)
        if (!empty($row->qr_path)) {
            $rel = ltrim((string)$row->qr_path, '/\\');
            // prevent traversal
            if (strpos($rel, '..') === false) {
                $abs = public_path($rel);
                if (is_file($abs)) {
                    @unlink($abs);
                }
            }
        }

        DB::table('meeting_link')->where('id', $id)->delete();

        return back()->with('success', 'Meeting link deleted successfully!');
    }

    /**
     * Improved meeting links interface
     */
    public function indexImproved()
    {
        $now = Carbon::now();
        $meetings = DB::table('meeting_link')->get();
        $stats = [
            'total' => $meetings->count(),
            'active' => $meetings->where('expires_at', '>', $now)->count(),
            'expired' => $meetings->where('expires_at', '<=', $now)->count(),
        ];
        
        return view('admin.meeting-links.index_improved', [
            'meetings' => $meetings,
            'stats' => $stats,
            'cspNonce' => app('csp_nonce'),
        ]);
    }
}
