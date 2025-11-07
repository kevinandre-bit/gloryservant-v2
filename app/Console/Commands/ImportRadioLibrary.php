<?php
// app/Console/Commands/ImportRadioLibrary.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportRadioLibrary extends Command
{
    protected $signature = 'radio:import-library {path?}';
    protected $description = 'Import/refresh tracks from a library CSV/JSON export';

    public function handle()
    {
        $path = $this->argument('path') ?? storage_path('app/library_export.csv');
        if (!file_exists($path)) {
            $this->error("Missing: {$path}");
            return self::FAILURE;
        }

        $isJson = strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'json';
        $rows   = $isJson ? json_decode(file_get_contents($path), true) : $this->parseCsv($path);

        // Header lookup tolerant to case/spaces/underscores/dots
        $lookup = function (array $row, $want) {
            $normalize = function ($s) {
                return strtolower(preg_replace('/[^\pL\pN]+/u', '', (string) $s));
            };
            $target = $normalize($want);
            foreach ($row as $k => $v) {
                if ($normalize($k) === $target) return $v;
            }
            return null;
        };

        // "HH:MM:SS" | "M:SS" | "SS" -> seconds (PHP 7.4-safe)
        $toSeconds = function ($hms) {
            if ($hms === null) return null;
            $hms = trim((string) $hms);
            if ($hms === '') return null;
            if (ctype_digit($hms)) return (int) $hms;
            $a = array_map('intval', explode(':', $hms));
            if (count($a) === 3) return $a[0]*3600 + $a[1]*60 + $a[2];
            if (count($a) === 2) return $a[0]*60 + $a[1];
            return '0';
        };

        $data = [];
        $skipped = 0;

        foreach ($rows as $r) {
            $fullName = $lookup($r, 'Full name');   // keep verbatim
            $title    = $lookup($r, 'Name');
            $performer= $lookup($r, 'performer');
            $year  = $lookup($r, 'Year');
            $duration = $lookup($r, 'Duration');     // optional
            $category = $lookup($r, 'Category');     // optional
            $theme    = $lookup($r, 'Theme');        // optional

            if ($fullName === null || trim((string)$fullName) === '') { $skipped++; continue; }

            // ext from the full path (no normalization/splitting beyond pathinfo)
            $ext = pathinfo((string)$fullName, PATHINFO_EXTENSION);
            $ext = $ext ? '.'.strtolower($ext) : null;


            $data[] = [
                'relative_path'    => (string) $fullName,                               // Full name verbatim
                'filename'         => $fullName,                                             // KEEP NULL
                'ext'              => $ext,                                             // derived from full path
                'title'            => ($title !== null && trim($title) !== '') ? (string)$title : null,
                'performer'        => ($performer !== null && trim($performer) !== '') ? (string)$performer : null,
                'category'         => ($category !== null && trim($category) !== '') ? (string)$category : null,
                'theme'            => ($theme !== null && trim($theme) !== '') ? (string)$theme : null,
                'year'             => $year,
                'duration_seconds' => $toSeconds($duration),
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }

        DB::transaction(function () use ($data) {
            DB::table('tracks')->upsert(
                $data,
                ['relative_path', 'filename'], // filename is NULL for all: dedupe by full name
                ['title','performer','category','theme','year','duration_seconds','ext','updated_at']
            );
        });

        $this->info('Imported rows: '.count($data).($skipped ? " (skipped: {$skipped} without Full name)" : ''));
        return self::SUCCESS;
    }

    private function parseCsv(string $path): array
    {
        $out = [];
        if (($h = fopen($path, 'r')) !== false) {
            $headers = fgetcsv($h);
            while (($row = fgetcsv($h)) !== false) {
                if (!is_array($headers)) continue;
                $out[] = @array_combine($headers, $row);
            }
            fclose($h);
        }
        return $out;
    }
}