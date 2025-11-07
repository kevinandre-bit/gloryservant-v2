<?php
// app/Http/Controllers/ClockController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Location;
use Carbon\Carbon;
use App\Classes\table;
use App\Http\Controllers\Controller;
use App\Models\DailyToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; 

class ClockController extends Controller
{
    /**
     * Display the clock-in/out page.
     */
    public function clock()
    {
        $settings = table::settings()->where('id', 1)->first();
        $cc       = $settings->clock_comment;
        $tz       = $settings->timezone;
        $tf       = $settings->time_format;
        $rfid     = $settings->rfid;

        return view('clock', compact('cc', 'tz', 'tf', 'rfid'));
    }

    /**
     * Handle clock-in/clock-out submission.
     * Enforce geofencing only for onsite users on the web.
     */
    public function add(Request $request)
    {
        // 1) Validation (manual, so we can return {"error": "..."} instead of default 422 shape)
        $rules = [
            'idno'            => 'required|string|max:20',
            'type'            => 'required|string|in:timein,timeout',
            'clockin_comment' => 'nullable|string|max:255',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
            'computer_name'   => 'nullable|string|max:100',
            'ts'              => 'nullable|date',
        ];
        if ($request->has('email')) {
            $rules['email'] = 'required|email';
        }

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            // Return 422 in the exact shape your macOS app expects
            return $this->jsonError($v->errors()->first(), 422);
        }
        $data = $v->validated();

        // Normalize/trim to avoid whitespace/case issues
        $data['email'] = isset($data['email']) ? trim(mb_strtolower($data['email'])) : null;
        $data['idno']  = isset($data['idno'])  ? trim($data['idno']) : null;

        // 2) User lookup (email path preferred if provided)
        if (!empty($data['email'])) {
            $user = User::whereRaw('LOWER(email)=?', [$data['email']])->first();
            if (!$user) {
                return $this->jsonError('No user found with that email.', 404);
            }
            if (strtoupper($data['idno']) !== strtoupper($user->idno)) {
                return $this->jsonError('Email and ID do not match.', 422);
            }
        } else {
            $user = User::whereRaw('UPPER(idno)=?', [strtoupper($data['idno'])])->first();
            if (!$user) {
                // API path → JSON; web fallback still keeps redirect behavior
                if ($request->wantsJson()) {
                    return $this->jsonError('You entered an invalid ID.', 422);
                }
                return redirect()->back()
                    ->withErrors(['idno' => 'You entered an invalid ID.'])
                    ->withInput();
            }
        }

        // 3) Common vars
        $idno     = strtoupper($data['idno']);
        $type     = $data['type'];
        $date     = date('Y-m-d');
        $timeRaw  = date('h:i:s A'); // 12h with AM/PM
        $tf       = table::settings()->value('time_format');
        $time     = $tf == 1 ? $timeRaw : date('H:i:s', strtotime($timeRaw));
        $comment  = isset($data['clockin_comment']) ? strtoupper($data['clockin_comment']) : '';
        $computer = $data['computer_name'] ?? 'web';

        // 4) Geofence for onsite, web-only
        $isOnsite = strtolower($user->work_type ?? 'onsite') === 'onsite';
        $isApi    = $request->expectsJson() || $request->is('api/*');  // <-- treat API as "app"
        $isApp    = $isApi || $request->has('email');                  // <-- broaden app mode

        if ($isOnsite && !$isApp) {
            if (empty($data['latitude']) || empty($data['longitude'])) {
                return $this->jsonError('Location data not available. Please enable location access.', 422);
            }
            $within = false;
            foreach (Location::all() as $loc) {
                $d = $this->haversineDistance(
                    $data['latitude'], $data['longitude'],
                    $loc->latitude,    $loc->longitude
                );
                if ($d <= $loc->radius) { $within = true; break; }
            }
            if (!$within) {
                return $this->jsonError('You are not within any allowed location to clock in or out.', 422);
            }
        }

        // 5) Employee display name (defensive: if missing person row)
        $emp = table::people()->where('id', $user->id)->first();
        if (!$emp) {
            return $this->jsonError('Employee record not found for this user.', 404);
        }
        $employee = mb_strtoupper("{$emp->lastname}, {$emp->firstname} {$emp->mi}");

        // 6) Time-in
        if ($type === 'timein') {
            if (table::attendance()->where([['reference', $user->id], ['date', $date]])->exists()) {
                $hti = table::attendance()->where([['reference', $user->id], ['date', $date]])->value('timein');
                $fmt = $tf == 1 ? date('h:i A', strtotime($hti)) : date('H:i', strtotime($hti));
                return $this->jsonError("{$employee}\nYou already Time In today at $fmt", 409);
            }
            if (table::attendance()->where([['reference',$user->id],['totalhours','']])->exists()) {
                return $this->jsonError('Please Clock Out from your last Clock In.', 409);
            }
            $sched  = table::schedules()->where([['reference',$user->id],['archive',0]])->value('intime');
            $status = ($sched && date('H.i', strtotime($timeRaw)) > date('H.i', strtotime($sched))) ? 'Late In' : 'In Time';

            table::attendance()->insert([[
                'idno'          => $idno,
                'reference'     => $user->id,
                'date'          => $date,
                'employee'      => $employee,
                'timein'        => "$date $timeRaw",
                'status_timein' => $status,
                'comment'       => $comment ?: null,
                'computer_name' => $computer,
            ]]);

            $payload = compact('type','time','date') + [
                'lastname'  => $emp->lastname,
                'firstname' => $emp->firstname,
                'mi'        => $emp->mi,
            ];
        }
        // 7) Time-out
        else {
            $open = table::attendance()->where([['reference',$user->id],['totalhours','']])->first(['timein','date']);
            if (!$open) {
                return $this->jsonError('Please Clock In before Clocking Out.', 409);
            }
            if (table::attendance()->where([['reference',$user->id],['date',$date],['timeout','<>',null]])->exists()) {
                $hto = table::attendance()->where([['reference',$user->id],['date',$date]])->value('timeout');
                $fmt = $tf == 1 ? date('h:i A', strtotime($hto)) : date('H:i', strtotime($hto));
                return $this->jsonError("{$employee}\nYou already Time Out today at $fmt", 409);
            }

            $timeOUT = date('Y-m-d h:i:s A', strtotime("$date $timeRaw"));
            $sched   = table::schedules()->where([['reference',$user->id],['archive',0]])->value('outime');
            $status  = ($sched && date('H.i', strtotime($timeOUT)) < date('H.i', strtotime($sched))) ? 'Early Out' : 'On Time';

            $t1 = Carbon::createFromFormat('Y-m-d h:i:s A', $open->timein);
            $t2 = Carbon::createFromFormat('Y-m-d h:i:s A', $timeOUT);
            $total = $t1->diffInHours($t2) . '.' . floor($t1->diffInMinutes($t2) % 60);
            if ($total > 15) $total = 6.0;

            table::attendance()->where([['reference',$user->id],['date',$open->date]])->update([
                'timeout'        => $timeOUT,
                'totalhours'     => $total,
                'status_timeout' => $status,
                'computer_name'  => $computer,
            ]);

            $payload = compact('type','time','date') + [
                'lastname'  => $emp->lastname,
                'firstname' => $emp->firstname,
                'mi'        => $emp->mi,
            ];
        }

        // 8) Return
        if ($request->wantsJson()) {
            return response()->json($payload);
        }
        return redirect()->back()->with('success', 'Attendance recorded.');
    }

    protected function jsonError(string $msg, int $code)
    {
        return response()->json(['error' => $msg], $code);
    }

    /** Haversine distance (m) */
    public function scanQr(Request $request)
{
    $token  = $request->query('token');
    $action = $request->query('action', 'timein');  // default to timein
    $today  = now()->toDateString();

    // 1) Validate QR token
    if (! DailyToken::where('date', $today)->where('token', $token)->exists()) {
        return redirect('/dashboard')
            ->with('clockError', 'Invalid or expired QR code.');
    }

    // 2) Prepare the “add” call
    $request->merge([
        'idno' => Auth::user()->idno,
        'type' => $action,  // either 'timein' or 'timeout'
    ]);

    // 3) Reuse your existing add() logic
    $response = $this->add($request);
    $payload  = $response->getOriginalContent();

    // 4a) On any error *not* about “already Time In”, just show it
    if (isset($payload['error']) && stripos($payload['error'], 'already Time In') === false) {
        return redirect('/dashboard')
            ->with('clockError', $payload['error']);
    }

    // 4b) If it’s the “already Time In” case, flash the token so we can offer “Clock Out”
    if (isset($payload['error']) && stripos($payload['error'], 'already Time In') !== false) {
        return redirect('/dashboard')
            ->with('clockError', $payload['error'])
            ->with('clockToken', $token);
    }

    // 4c) On success (for either timein or timeout), flash date & time
    $date = $payload['date'] ?? now()->toDateString();
    $time = $payload['time'] ?? now()->format('H:i:s');

    return redirect('/dashboard')
        ->with('clockSuccess', compact('date','time'))
        ->with('clockAction', $action);
}

    public function showQr()
{
    $today = now()->toDateString();
    $path  = storage_path("app/public/qrcodes/{$today}.svg");

    if (!file_exists($path)) {
        // Optionally trigger generation on-the-fly
        Artisan::call('qr:generate-daily');
    }

    $svg = file_get_contents($path);
    return view('admin.index', compact('svg'));
}
    private function haversineDistance($lat1,$lon1,$lat2,$lon2){
        $R = 6371000;
        $dLat=deg2rad($lat2-$lat1);
        $dLon=deg2rad($lon2-$lon1);
        $a = sin($dLat/2)**2
           +cos(deg2rad($lat1))*cos(deg2rad($lat2))
           *sin($dLon/2)**2;
        return $R*2*asin(sqrt($a));
    }
}