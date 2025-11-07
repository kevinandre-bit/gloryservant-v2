<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /** Return [Collection $notifications, int $unreadCount] for the navbar */
    public function forNav(): array
    {
            $userId = Auth::id();

            if (!$userId) {
                return [collect(), 0];
            }

            $notifications = DB::table('notifications')
        ->join('notification_targets', 'notification_targets.notification_id', '=', 'notifications.id')
        ->where('notification_targets.user_id', $userId)
        ->orderByDesc('notifications.created_at') // newest first
        ->limit(10)
        ->select([
            'notifications.id',
            'notifications.type',
            'notifications.title',
            'notifications.body',
            'notifications.url',
            'notifications.icon',
            'notifications.created_at',
            'notification_targets.is_read',
        ])
        ->get();

    $unreadCount = DB::table('notification_targets')
        ->where('user_id', $userId)
        ->where('is_read', 0)
        ->count();

    return [$notifications, $unreadCount];

        // (keep your markAllRead / markOneRead here if you already added them)
    }

}

