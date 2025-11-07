<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPortalController extends Controller
{
    public function enter()
    {
        $sub = DB::table('vw_user_campus_subdomain')
            ->where('user_id', Auth::id())
            ->value('subdomain');

        if (!$sub) {
            return redirect()->route('home')
                ->withErrors("Aucun campus associé à ce compte.");
        }

        $host = $sub.'.'.config('app.base_domain');
        return redirect()->away('https://'.$host.'/admin');
    }
}