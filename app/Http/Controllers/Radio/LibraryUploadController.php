<?php

namespace App\Http\Controllers\Radio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use App\Models\PlaylistItem;
use App\Models\Track;

class LibraryUploadController extends Controller
{
    public function showForm()
    {
        return view('radio_dashboard.Program.library.upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'library_file' => 'required|file|mimes:csv,txt,json|max:10240',
        ]);

        // Save the file into storage/app/imports with a randomized name to avoid overwrite
        $file = $request->file('library_file');
        $ext = strtolower($file->getClientOriginalExtension());
        $path = $file->storeAs('imports', 'library_'.Str::uuid().'.'.$ext); // storage/app/imports/...

        // Run the Artisan import command pointing to absolute path
        \Artisan::call('radio:import-library', ['path' => storage_path('app/'.$path)]);

        return redirect()->route('program.library.index')
            ->with('success', "Library uploaded and imported successfully.");
    }

    public function clear()
{
    // Delete playlist items first (due to FK constraint)
    PlaylistItem::query()->delete();

    // Now delete tracks
    Track::query()->delete();

    return back()->with('success', __('Library cleared successfully.'));
}

// app/Http/Controllers/Radio/LibraryUploadController.php

protected function mapCsvRow(array $row): array
{
    // Expecting headers: Full name, Name, (maybe Duration, Performer, Category, Theme, Year)
    $full = trim((string)($row['Full name'] ?? '')); // e.g. "shows/morning/track_a.wav"
    $name = trim((string)($row['Name'] ?? ''));      // human title

    // Split into relative_path + filename
    [$relativePath, $filename] = $this->splitPath($full);

    return [
        'filename'         => $filename ?: ($name ?: ''), // fallback to Name if filename missing
        'relative_path'    => $relativePath ?: null,
        'title'            => $name ?: null,              // optional extra column if you added it
        'performer'        => $row['Performer']   ?? null, // will be null/blank in your sheet
        'category'         => $row['Category']    ?? null,
        'theme'            => $row['Theme']       ?? null,
        'year'             => $row['Year']        ?? null, // blank -> null
        'duration_seconds' => $this->toSeconds($row['Duration'] ?? null), // if present
    ];
}

private function splitPath(string $full): array
{
    // Normalize slashes
    $full = str_replace('\\', '/', $full);
    $full = ltrim($full, '/');

    if ($full === '') return [null, ''];

    $parts = explode('/', $full);
    $filename = array_pop($parts);
    $rel = implode('/', $parts);

    return [$rel ?: null, $filename ?: ''];
}

private function toSeconds($hms): ?int
{
    if (!$hms) return null;
    $hms = trim((string)$hms);
    if ($hms === '') return null;

    // Accept "HH:MM:SS" or "M:SS" or plain seconds
    if (ctype_digit($hms)) return (int)$hms;

    $chunks = array_map('intval', explode(':', $hms));
    if (count($chunks) === 3) return $chunks[0]*3600 + $chunks[1]*60 + $chunks[2];
    if (count($chunks) === 2) return $chunks[0]*60 + $chunks[1];
    return null;
}
}
