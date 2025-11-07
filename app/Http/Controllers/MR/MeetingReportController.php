<?php

// app/Http/Controllers/MR/MeetingReportController.php
namespace App\Http\Controllers\MR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MeetingReportController extends Controller
{
    // GET /mr/{token}
    public function show(string $token)
    {   
        $tokenHash = hash('sha256', $token);

    $magic = \DB::table('mr_magic_links')
        ->where('token_hash', $tokenHash)
        ->first();

    abort_unless($magic, 404, 'Invalid meeting link');

    $meeting = \DB::table('mr_meetings')->where('id', $magic->meeting_id)->first();
    abort_unless($meeting, 404, 'Meeting not found');

        $agenda = DB::table('mr_agenda_items')->where('meeting_id', $meeting->id)
                    ->orderBy('started_at')->get();

        $attendees = DB::table('mr_attendees')->where('meeting_id', $meeting->id)
                    ->orderBy('attendee_name')->get();

        $tasks = DB::table('mr_tasks')->where('meeting_id', $meeting->id)
                    ->orderByDesc('id')->limit(50)->get();

        $projects = DB::table('mr_asana_mappings')->orderBy('project_name')->get();

        $notes = DB::table('mr_meeting_notes')->where('meeting_id', $meeting->id)->first();

        return view('mr.show', [
            'meeting'    => $meeting,
            'agenda'     => $agenda,
            'attendees'  => $attendees,
            'tasks'      => $tasks,
            'projects'   => $projects,
            'notes'      => $notes,
        ]);
    }

    // POST /mr/{meeting}/notes
    public function saveNotes(Request $req, int $meeting)
    {
        $data = $req->validate([
            'content' => 'nullable|string',
        ]);
        $content = (string) ($data['content'] ?? '');
        // Sanitize note HTML to mitigate stored-XSS. Allow a conservative set of tags only.
        $content = $this->sanitizeHtml($content);
        $now = now();

        $exists = DB::table('mr_meeting_notes')->where('meeting_id', $meeting)->first();

        if ($exists) {
            DB::table('mr_meeting_notes')
              ->where('meeting_id', $meeting)
              ->update([
                  'content'    => $content,
                  'updated_at' => $now,
              ]);
        } else {
            DB::table('mr_meeting_notes')->insert([
                'meeting_id' => $meeting,
                'content'    => $content,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    // POST /mr/{meeting}/notes/upload  (CKEditor simpleUpload)
    public function uploadNoteAsset(Request $req, int $meeting)
    {
        $file = $req->file('upload') ?? $req->file('file');
        abort_unless($file && $file->isValid(), 422, 'No file uploaded');

        // Disallow SVG to prevent stored XSS via embedded scripts
        $allowed = ['image/png','image/jpeg','image/webp','image/gif'];
        abort_unless(in_array($file->getMimeType(), $allowed), 422, 'Unsupported file type');

        $path = $file->store("mr_notes/{$meeting}", 'public'); // php artisan storage:link
        $url  = asset('storage/'.$path);

        return response()->json(['uploaded' => true, 'url' => $url]);
    }

    // --------- Stubs (make UI usable; fill later) ----------
    public function publish(Request $req, int $meeting) {
        // TODO: push to Google Docs and return URL
        return response()->json(['url' => '#', 'ok' => true]);
    }
    public function agendaStart(int $meeting, int $item) { return response()->json(['ok'=>true]); }
    public function agendaStop (int $meeting, int $item) { return response()->json(['ok'=>true]); }
    public function agendaToggle(int $meeting, int $item) { return response()->json(['ok'=>true]); }

    public function attendeeAdd(Request $r, int $meeting) {
        $data = $r->validate([
            'name'  => 'required|string|max:120',
            'email' => 'nullable|email|max:255',
        ]);
        $id = DB::table('mr_meeting_attendees')->insertGetId([
            'meeting_id'      => $meeting,
            'attendee_name'   => $data['name'],
            'attendee_email'  => $data['email'] ?? null,
            'status'          => 'present',
            'checked_in_at'   => now(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
        return response()->json(['id'=>$id]);
    }

    public function attendeeStatus(Request $r, int $meeting, int $attendee) {
        $data = $r->validate([
            'status' => 'required|in:present,absent,excused,late',
        ]);
        DB::table('mr_meeting_attendees')->where('id',$attendee)->update([
            'status'     => $data['status'],
            'updated_at' => now(),
        ]);
        return response()->json(['ok'=>true]);
    }

    public function taskCreate(Request $r, int $meeting) {
        $data = $r->validate([
            'title'  => 'required|string|max:200',
            'due_on' => 'nullable|date',
        ]);
        $id = DB::table('mr_meeting_tasks')->insertGetId([
            'meeting_id'          => $meeting,
            'title'               => $data['title'],
            'asana_task_gid'      => null,
            'asana_permalink_url' => null,
            'due_on'              => $data['due_on'] ?? null,
            'last_seen_status'    => 'incomplete',
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
        return response()->json(['id'=>$id,'url'=>null]);
    }

    public function taskLink(Request $r, int $meeting) {
        $data = $r->validate([
            'title'                => 'required|string|max:200',
            'asana_task_gid'       => 'required|string|max:64',
            'asana_permalink_url'  => 'required|url|max:2048',
        ]);
        $id = DB::table('mr_meeting_tasks')->insertGetId([
            'meeting_id'          => $meeting,
            'title'               => $data['title'],
            'asana_task_gid'      => $data['asana_task_gid'],
            'asana_permalink_url' => $data['asana_permalink_url'],
            'due_on'              => null,
            'last_seen_status'    => 'incomplete',
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
        return response()->json(['id'=>$id]);
    }

    public function taskComplete(int $meeting, int $task) {
        DB::table('mr_meeting_tasks')->where('id',$task)->update([
            'last_seen_status'=>'completed','updated_at'=>now()
        ]);
        return response()->json(['ok'=>true]);
    }

    private function sanitizeHtml(string $html): string
    {
        // Remove script/style blocks
        $html = preg_replace('#<\s*(script|style)[^>]*>.*?<\s*/\s*\1\s*>#is', '', $html ?? '');
        // Allow a conservative set of tags; strip all attributes
        $allowed = '<p><br><b><strong><i><em><ul><ol><li><blockquote><code><pre><h1><h2><h3>';
        $clean = strip_tags($html, $allowed);
        // Truncate to a sane length to avoid abuse
        return mb_substr($clean, 0, 20000);
    }
}
