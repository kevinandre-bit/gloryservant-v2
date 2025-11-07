<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('admin.nav', function ($view) {
            $userId = Auth::id(); // swap to Auth::guard('admin')->id() if you use an admin guard

            $notifications = collect();
            $unreadCount   = 0;

            if ($userId) {
                $notifications = DB::table('notifications')
                    ->join('notification_targets', 'notification_targets.notification_id', '=', 'notifications.id')
                    ->where('notification_targets.user_id', $userId)
                    ->orderByDesc('notifications.created_at')
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
            }

            $view->with([
                'navNotifications' => $notifications,
                'navUnreadCount'   => $unreadCount,
            ]);
        });
    }
}