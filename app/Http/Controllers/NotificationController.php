<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function list(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['error'=>'unauthorized'], 401);
        $uid = $user->id;

        $rows = DB::table('notifications as n')
            ->join('notification_targets as t', 't.notification_id', '=', 'n.id')
            ->where('t.user_id', $uid)
            ->orderByDesc('n.id')
            ->limit(20)
            ->get([
                'n.id','n.type','n.title','n.body','n.url','n.icon','n.created_at',
                't.is_read','t.read_at'
            ]);

        return response()->json($rows);
    }

    public function count(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['unread'=>0], 401);
        $uid = $user->id;

        $count = DB::table('notification_targets')
            ->where('user_id', $uid)
            ->where('is_read', 0)
            ->count();

        return response()->json(['unread' => $count]);
    }

    public function read(Request $request, $id)
    {
        DB::table('notification_targets')
            ->where('notification_id', $id)
            ->where('user_id', $request->user()->id)
            ->update(['is_read' => 1, 'read_at' => now()]);

        return response()->noContent();
    }

    public function readAll(Request $request)
    {
        DB::table('notification_targets')
            ->where('user_id', $request->user()->id)
            ->where('is_read', 0)
            ->update(['is_read' => 1, 'read_at' => now()]);

        return response()->noContent();
    }
}
