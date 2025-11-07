<?php
namespace App\Http\Controllers\MR\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MeetingAdminController extends Controller
{
    public function create()
    {
        // Minimal TZ list (extend if needed)
        $timezones = [
    // Haiti / Caribbean
    'America/Port-au-Prince'   => 'Port-au-Prince, Haiti (GMT-5/4)',

    // United States
    'America/New_York'         => 'Florida (Eastern), New York (GMT-5/4)',
    'America/Chicago'          => 'Central Florida / Chicago (GMT-6/5)',
    'America/Denver'           => 'Denver (GMT-7/6)',
    'America/Los_Angeles'      => 'Los Angeles (GMT-8/7)',
];


        // Tiny templates for quick seed
        $agendaTemplates = [
            'comms' => "Prayer | 1\nAttendance Link | 2\nReview Key Metrics | 15\nDistributed Content Strategy | 20\nEvents Deadlines | 5\nReview Meeting Tasks | 5\nClosing Prayer | 1",
            'radio' => "Prayer | 1\nAttendance Link | 2\nReview Numbers | 5\nChecklist Progress | 5\nNew Ideas/Strategy | 20\nReview Tasks | 5\nClosing Prayer | 1",
            'empty' => "",
        ];

        return view('mr.admin.create', compact('timezones','agendaTemplates'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'title'      => 'required|string|max:255',
            'date'       => 'required|date',
            'meeting_type_id'      => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i',
            'timezone'   => 'required|string|max:64',
            'role'       => 'required|in:editor,viewer',
            'attendees'  => 'nullable|string',
            'agenda'     => 'nullable|string',
        ]);

        $startsAt = Carbon::parse($r->date.' '.$r->start_time, $r->timezone)->timezone('UTC');
        $endsAt   = $r->end_time ? Carbon::parse($r->date.' '.$r->end_time, $r->timezone)->timezone('UTC') : null;

        DB::beginTransaction();
        try {
            // Insert meeting (include only columns that exist)
            $columns = Schema::getColumnListing('mr_meetings');

            $meetingData = [
                'title'      => $r->title,
                'meeting_type_id' => $r->meeting_type_id,
                'starts_at'  => $startsAt,
                'ends_at'    => $endsAt,
                'timezone'   => $r->timezone,
                'status'     => 'scheduled',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Optional fields if your table has them
            if (in_array('team_name', $columns)) $meetingData['team_name'] = $r->input('team_name');
            if (in_array('show_name', $columns)) $meetingData['show_name'] = $r->input('show_name');

            $meetingId = DB::table('mr_meetings')->insertGetId($meetingData);

            // Create magic link token (plaintext + hash if both columns exist)
            $tokenPlain = Str::uuid()->toString(); // shareable token in URL
            $tokenHash  = hash('sha256', $tokenPlain);

            $mlCols = Schema::getColumnListing('mr_magic_links');
            $magic = [
                'meeting_id'   => $meetingId,
                'token_hash'   => $tokenHash,
                'role'         => $r->role,
                'created_at'   => now(),
                'is_revoked'   => 0,
                'created_ip'   => request()->ip(),
            ];
            if (in_array('token', $mlCols))       { $magic['token'] = $tokenPlain; }
            if (in_array('expires_at', $mlCols) && $r->filled('expires_at')) {
                $magic['expires_at'] = Carbon::parse($r->input('expires_at'), $r->timezone)->timezone('UTC');
            }

            DB::table('mr_magic_links')->insert($magic);

            // Seed attendees (one per line: "Name <email>" or "Name")
            $attLines = preg_split('/\r\n|\r|\n/', trim($r->input('attendees','')));
            foreach ($attLines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                preg_match('/^(.*?)(?:\\s*<([^>]+)>)?$/', $line, $m);
                $name  = trim($m[1] ?? $line);
                $email = isset($m[2]) ? trim($m[2]) : null;

                DB::table('mr_meeting_attendees')->insert([
                    'meeting_id'     => $meetingId,
                    'attendee_name'  => $name,
                    'attendee_email' => $email,
                    'status'         => 'present',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            // Seed agenda (one per line: "Title | minutes")
            $agLines = preg_split('/\r\n|\r|\n/', trim($r->input('agenda','')));
            $order = 1;
            foreach ($agLines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                $parts = array_map('trim', explode('|', $line));
                $title = $parts[0] ?? $line;
                $mins  = isset($parts[1]) ? (int)$parts[1] : 0;

                DB::table('mr_agenda_items')->insert([
                    'meeting_id'      => $meetingId,
                    'title'           => $title,
                    'planned_minutes' => $mins,
                    'display_order'   => $order,
                    'position'        => $order,   // <— add this
                    'is_covered'      => 0,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
                $order++;
            }

            DB::commit();

            // Build shareable URL
            $url = route('mr.show', ['token' => $tokenPlain]);

            return redirect()
                ->route('mr.admin.meetings.create')
                ->with('success', 'Meeting created')
                ->with('created_link', $url);

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors('Failed to create meeting: '.$e->getMessage())->withInput();
        }
    }
}
