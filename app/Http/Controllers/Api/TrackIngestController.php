<?php
// app/Http/Controllers/Api/TrackIngestController.php
// app/Http/Controllers/Api/TrackIngestController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackIngestController extends Controller
{
    // app/Http/Controllers/Api/TrackIngestController.php

public function ingest(Request $request)
{
    // ---- auth (unchanged) ----
    $expected = (string) config('services.ingest.token', '');
    $provided = $request->header('X-INGEST-TOKEN')
        ?? $request->header('X-Ingest-Token')
        ?? $request->header('x-ingest-token');

    if (!$provided) {
        $auth = $request->header('Authorization') ?? $request->header('authorization');
        if ($auth && stripos($auth, 'Bearer ') === 0) {
            $provided = trim(substr($auth, 7));
        }
    }
    // Do not accept token via query/body to reduce leakage risk
    $provided = $provided ?? '';
    $provided = is_string($provided) ? trim($provided) : '';
    if ($expected === '' || !hash_equals($expected, $provided)) {
        return response()->json(['ok' => false, 'error' => 'unauthorized'], 401, ['Cache-Control' => 'no-store']);
    }

    $rows = (array) $request->input('rows', []);
    if (!$rows) return response()->json(['ok' => true, 'imported' => 0]);

    // ---- helpers like CSV path ----
    $norm = fn($s) => strtolower(preg_replace('/[^\pL\pN]+/u', '', (string)$s));
    $col  = function(array $row, string $want) use ($norm) {
        $wantN = $norm($want);
        foreach ($row as $k => $v) if ($norm($k) === $wantN) return $v;
        return null;
    };

    // identical to your upload controller semantics:
    // - blank → null
    // - accept "HH:MM:SS"/"M:SS"/"SS"
    // - also accept numeric (Excel serial days) from Sheets
   // helpers (keep these near top of method)
$trim = function ($v, $len) {
    $s = trim((string)$v);
    if ($s === '') return null;
    $s = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $s);
    return mb_substr($s, 0, $len);
};
$toSeconds = function ($hms) {
    if ($hms === null) return 0;
    $hms = trim((string)$hms);
    if ($hms === '') return 0;
    if (ctype_digit($hms)) return (int)$hms;
    $a = array_map('intval', explode(':', $hms));
    if (count($a) === 3) return $a[0]*3600 + $a[1]*60 + $a[2];
    if (count($a) === 2) return $a[0]*60 + $a[1];
    return 0; // anything unrecognized => 0
};

$clean = [];
foreach ((array)$request->input('rows', []) as $r) {
    // column accessor tolerant to header casing/spaces
    $get = function($want) use ($r) {
        $norm = fn($s)=>strtolower(preg_replace('/[^\pL\pN]+/u','',(string)$s));
        $wantN = $norm($want);
        foreach ($r as $k=>$v) if ($norm($k)===$wantN) return $v;
        return null;
    };

    $fullName  = $get('Full name');   // required unique key
    if (!$fullName) continue;

    // Skip obvious junk
    if (stripos($fullName, '$RECYCLE.BIN') !== false) continue;

    $title     = $get('Name');
    $performer = $get('Performer');
    $category  = $get('Category');
    $theme     = $get('Theme');
    $yearRaw   = $get('Year');
    $durStr    = $get('Duration');

    $duration  = $toSeconds($durStr);             // 0 if blank/bad
    $ext = null; $dot = strrpos((string)$fullName, '.');
    if ($dot !== false) $ext = '.'.strtolower(substr((string)$fullName, $dot+1));

    $row = [
        'relative_path'    => $trim($fullName, 255),
        'filename'         => $trim($fullName, 255),
        'ext'              => $trim($ext, 16),
        'title'            => $trim($title, 255),
        'performer'        => $trim($performer, 255),
        'category'         => $trim($category, 100),
        'theme'            => $trim($theme, 100),
        'year'             => ($yearRaw !== null && $yearRaw !== '') ? (int)$yearRaw : null,
        'duration_seconds' => max(0, (int)$duration),   // NEVER NULL
        'created_at'       => now(),
        'updated_at'       => now(),
    ];

    // Final guard: if for any reason duration_seconds is null, fix to 0
    if (!isset($row['duration_seconds']) || $row['duration_seconds'] === null) {
        $row['duration_seconds'] = 0;
    }

    $clean[] = $row;
}

if (!$clean) return response()->json(['ok'=>true,'imported'=>0]);

try {
    \DB::table('tracks')->upsert(
        $clean,
        ['relative_path'],
        ['ext','title','performer','category','theme','year','duration_seconds','updated_at']
    );
} catch (\Throwable $e) {
    return response()->json([
        'ok' => false,
        'error' => 'db_upsert_failed',
        'message' => $e->getMessage(),
    ], 500);
}

return response()->json(['ok'=>true,'imported'=>count($clean)]);
}

}
