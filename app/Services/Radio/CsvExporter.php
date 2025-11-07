<?php
// app/Services/Radio/CsvExporter.php
namespace App\Services\Radio;

use App\Models\Playlist;
use Illuminate\Support\Facades\Storage;

class CsvExporter
{
    /**
     * IMPORTANT: Replace these with EXACT headers from your playout CSV.
     * Example placeholders below — copy from your sample export.
     */
    private array $headers = [
        'FilePath',        // e.g., full path or filename column used by your playout
        'Title',           // or the exact header name
        'Category',        // or the exact header name
        // ... add all other required columns in correct order
    ];

    public function make(Playlist $playlist): string
    {
        $contentRoot = config('radio.content_root');
        $delimiter   = config('radio.export_delimiter', ',');

        $fp = fopen('php://temp','r+');
        fputcsv($fp, $this->headers, $delimiter);

        foreach ($playlist->items as $item) {
            $t   = $item->track;
            $rel = trim((string)$t->relative_path, "\\/");
            $full= $contentRoot
                . (strlen($rel) ? DIRECTORY_SEPARATOR.$rel : '')
                . DIRECTORY_SEPARATOR . $t->filename;

            // Build row per your playout expectations
            $row = [
                'FilePath' => $full, // or just $t->filename if required
                'Title'    => $t->title ?: pathinfo($t->filename, PATHINFO_FILENAME),
                'Category' => $t->category ?: '',
                // ... set defaults for any additional required columns
            ];

            fputcsv($fp, array_map(fn($h)=>$row[$h] ?? '', $this->headers), $delimiter);
        }

        rewind($fp); $csv = stream_get_contents($fp); fclose($fp);
        return $csv;
    }

    public function store(Playlist $playlist, string $csv): string
    {
        $dir = config('radio.export_dir','playlist_exports');
        $name = sprintf('%s/playlist_%d_%s.csv', $dir, $playlist->id, now()->format('Ymd_His'));
        Storage::disk('local')->put($name, $csv);
        return $name;
    }
}