<?php

/*namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RadioCheckinIngestController extends Controller
{
    public function store(Request $request)
    {
        $expected = trim((string) config('services.radio_checkins.webhook_token'));
        $token = $request->header('X-Webhook-Token')
              ?? $request->bearerToken()
              ?? $request->input('token')
              ?? $request->query('token');
        $token = is_string($token) ? trim($token) : '';

        abort_unless(hash_equals($expected, $token), 401, 'Unauthorized');

        $data = $request->validate([
            'rows'                  => 'required|array|min:1',
            'rows.*.station_id'     => 'required|integer',
            'rows.*.user_id'        => 'nullable|integer',
            'rows.*.status' => 'required|string|max:50',
            'rows.*.note'           => 'nullable|string',
            'rows.*.created_at'     => 'nullable|date',
            'rows.*.updated_at'     => 'nullable|date',
        ]);

        $now = now();
        $rows = array_map(function ($r) use ($now) {
            $created = !empty($r['created_at']) ? Carbon::parse($r['created_at']) : ($r['updated_at'] ?? $now);
            $updated = !empty($r['updated_at']) ? Carbon::parse($r['updated_at']) : $now;

            return [
                'station_id' => (int)$r['station_id'],
                'user_id'    => array_key_exists('user_id',$r) && $r['user_id'] !== null ? (int)$r['user_id'] : null,
                'status'     => $r['status'],
                'note'       => $r['note'] ?? null,
                'created_at' => $created,
                'updated_at' => $updated,
            ];
        }, $data['rows']);

        DB::table('radio_checkins')->insert($rows);

        return response()->json(['ok' => true, 'inserted' => count($rows)], 200, ['Cache-Control' => 'no-store']);
    }
}*/