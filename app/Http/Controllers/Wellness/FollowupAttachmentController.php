<?php

namespace App\Http\Controllers\Wellness;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FollowupAttachmentController extends Controller
{
    public function download(int $id, int $attachmentId = null)
    {
        // Authorize by followup id
        abort_unless(Gate::allows('download-followup', $id), 403);

        // This assumes a single attachment stored on the followup row.
        // If you have multiple attachments in another table, adjust lookup.
        $row = DB::table('volunteer_followups')->where('id',$id)->first();
        abort_if(!$row, 404);

        if (!$row->attachment_path) {
            abort(404, 'No attachment.');
        }

        $disk = Storage::disk('private');
        if (!$disk->exists($row->attachment_path)) {
            abort(404, 'File missing.');
        }

        $filename = $row->attachment_name ?: basename($row->attachment_path);

        return new StreamedResponse(function() use ($disk, $row) {
            $stream = $disk->readStream($row->attachment_path);
            fpassthru($stream);
            if (is_resource($stream)) fclose($stream);
        }, 200, [
            'Content-Type'        => $disk->mimeType($row->attachment_path) ?? 'application/octet-stream',
            'Content-Length'      => (string)($disk->size($row->attachment_path) ?? ''),
            'Content-Disposition' => 'attachment; filename="'.addslashes($filename).'"',
        ]);
    }
}