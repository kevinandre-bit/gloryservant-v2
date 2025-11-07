<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use App\Classes\permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Mail\NotificationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Notification as NotificationModel;
class SendMailController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','admin'])->only([
            'index','sendNotification','duplicateNotification','listNotifications','fetchNotifications','markAllNotificationsRead'
        ]);
    }
    
// Display the Send Mail page
// This method retrieves the authenticated user ID and fetches user, ministry, and campus data
// It also fetches notification counts and detailed notification data for the user.
public function index()
{
    $userId = Auth::id(); // Get the authenticated user's ID

    // Retrieve all users except the authenticated user
    $users = DB::table('users')
        ->select('id', 'name')
        ->where('id', '<>', $userId)
        ->get();

    // Retrieve all ministries
    $ministries = DB::table('tbl_form_ministry')
        ->select('id', 'ministry')
        ->get();

    // Retrieve all campuses
    $campuses = DB::table('tbl_form_campus')
        ->select('id', 'campus')
        ->get();

    // Retrieve notification counts (unread and read)
    $notificationCounts = DB::table('notifications')
        ->select(DB::raw("count(case when status = 'unread' then 1 end) as unread_count, count(case when status = 'read' then 1 end) as read_count"))
        ->where('sender_id', $userId)
        ->first();

    // Retrieve detailed notification data for the authenticated user
    $notifications = DB::table('notifications')
        ->select(
            'notification_code',
            DB::raw('MAX(id) as id'),
            DB::raw('JSON_UNQUOTE(JSON_EXTRACT(MAX(data), "$.message")) as message'),
            DB::raw('JSON_UNQUOTE(JSON_EXTRACT(MAX(data), "$.target_name")) as target_name'),
            'target_type',
            DB::raw('COUNT(id) as recipient_count'),
            DB::raw('SUM(CASE WHEN status = "unread" THEN 1 ELSE 0 END) as unread_count'),
            DB::raw('SUM(CASE WHEN status = "read" THEN 1 ELSE 0 END) as read_count'),
            DB::raw('ROUND(SUM(CASE WHEN status = "read" THEN 1 ELSE 0 END) / COUNT(id) * 100, 2) as rate'),
            DB::raw('MAX(created_at) as sent_at')
        )
        ->where('sender_id', $userId)
        ->groupBy('notification_code', 'target_type')
        ->orderBy('sent_at', 'desc')
        ->get();

    // Return the data to the 'admin.sendMail' view
    return view('admin.sendMail', compact('users', 'ministries', 'campuses', 'notificationCounts', 'notifications'));
}


/**
 * Generate a Unique Notification Code
 * This method generates a unique notification code in the format "NT-XXXXXXXXXXXXX".
 * The generated code is checked for uniqueness in the 'notifications' table.
 *
 * @return string The generated unique notification code
 */
private function generateUniqueNotificationCode()
{
    do {
        // Generate a random 13-character alphanumeric string
        $randomString = strtoupper(Str::random(13));
        $code = 'NT-' . $randomString;

        // Check if the generated code already exists in the database
        $exists = DB::table('notifications')->where('notification_code', $code)->exists();
    } while ($exists);

    return $code; // Return the unique code
}


// Send Notification Method
// This method handles the creation and sending of notifications based on the target type (user, campus, or ministry).
// It validates the request data, determines the target recipients, and inserts notification records in the database.

public function sendNotification(Request $request)
{
    $userId = Auth::id();

    $request->validate([
        'target_type'   => 'required|string|in:user,campus,ministry',
        'message'       => 'required|string|max:255',
    ]);

    $targetType      = $request->target_type;
    $message         = $request->message;
    $receiverIds     = [];
    $notificationLink = url('/sendMail');
    $notifiableType   = 'notification';

    // Generate the formatted notification code
    $notificationCode = $this->generateUniqueNotificationCode();

    // Base payload
    $notificationData = [
        'notification_code' => $notificationCode,
        'message'           => $message,
        'url'               => $notificationLink,
        'target_type'       => $targetType,
        'sender_id'         => $userId,
    ];

    try {
        DB::beginTransaction();

        // 1) Single user
        if ($targetType === "user" && $request->filled('receiver_id')) {
            $receiver = User::find($request->receiver_id);

            if ($receiver) {
                $notificationData['target_name']  = $receiver->name;
                $notificationData['target_value'] = $receiver->id;
                $receiverIds[]                     = $receiver->id;
            }
        }

        // 2) By campus
        elseif ($targetType === "campus" && $request->filled('campus_id')) {
            $campusName = DB::table('tbl_form_campus')
                             ->where('id', $request->campus_id)
                             ->value('campus');

            if ($campusName) {
                $notificationData['target_name']  = $campusName;
                $notificationData['target_value'] = $request->campus_id;

                $references = DB::table('tbl_campus_data')
                                ->whereRaw('LOWER(campus) = ?', [strtolower($campusName)])
                                ->pluck('reference')
                                ->toArray();

                $users = User::whereIn('reference', $references)->get();
                foreach ($users as $user) {
                    $receiverIds[] = $user->id;
                }
            }
        }

        // 3) By ministry
        elseif ($targetType === "ministry" && $request->filled('ministry_id')) {
            $ministryName = DB::table('tbl_form_ministry')
                                ->where('id', $request->ministry_id)
                                ->value('ministry');

            if ($ministryName) {
                $notificationData['target_name']  = $ministryName;
                $notificationData['target_value'] = $request->ministry_id;

                $references = DB::table('tbl_campus_data')
                                ->whereRaw('LOWER(ministry) = ?', [strtolower($ministryName)])
                                ->pluck('reference')
                                ->toArray();

                $users = User::whereIn('reference', $references)->get();
                foreach ($users as $user) {
                    $receiverIds[] = $user->id;
                }
            }
        }

        if (empty($receiverIds)) {
            DB::rollBack();
            return redirect()->back()->with('error', 'No recipients found for the selected target.');
        }

        // 4) Insert + send
        foreach ($receiverIds as $rid) {
            // insert notification record
            DB::table('notifications')->insert([
                'notification_code' => $notificationCode,
                'sender_id'         => $userId,
                'receiver_id'       => $rid,
                'notifiable_id'     => $rid,
                'message'           => $message,
                'type'              => $notifiableType,
                'target_type'       => $targetType,
                'data'              => json_encode($notificationData),
                'status'            => 'unread',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // now send the email
            $user = User::find($rid);
            if ($user && ! empty($user->email)) {
                try {
                    Mail::to($user->email)
                        ->send(new NotificationEmail($notificationData));
                } catch (\Throwable $mailEx) {
                    \Log::error("Failed sending notification email to {$user->email}: ".$mailEx->getMessage());
                    // continue to next user
                }
            }
        }

        DB::commit();
        return redirect()->back()->with('success', 'Notification sent successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Notification Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Notification failed: ' . $e->getMessage());
    }
}


// Duplicate Notification Method
// This method duplicates an existing notification by its notification code.
public function duplicateNotification($notification_code)
{
    // Retrieve the notification record
    $notification = DB::table('notifications')->where('notification_code', $notification_code)->first();

    if (!$notification) {
        return redirect()->back()->with('error', 'Notification not found.');
    }

    // Generate a new notification code
    $newNotificationCode = Str::uuid()->toString();

    // Insert the duplicate notification
    DB::table('notifications')->insert([
        'notification_code' => $newNotificationCode,
        'sender_id' => $notification->sender_id,
        'receiver_id' => $notification->receiver_id,
        'notifiable_id' => $notification->notifiable_id,
        'message' => $notification->message,
        'type' => $notification->type,
        'target_type' => $notification->target_type,
        'data' => $notification->data,
        'status' => 'unread',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return redirect()->back()->with('success', 'Notification duplicated successfully.');
}


// List Notifications Method
// This method retrieves the list of notifications sent by the authenticated user.
public function listNotifications()
{
    $userId = Auth::id(); // Get the authenticated user's ID

    // Fetch notifications data for the user
    $notifications = DB::table('notifications')
        ->select(
            'notification_code',
            'message',
            DB::raw('JSON_UNQUOTE(JSON_EXTRACT(data, "$.target_name")) as target_name'),
            'target_type',
            DB::raw('COUNT(id) as recipient_count'),
            DB::raw('SUM(CASE WHEN status = "unread" THEN 1 ELSE 0 END) as unread_count'),
            DB::raw('SUM(CASE WHEN status = "read" THEN 1 ELSE 0 END) as read_count'),
            DB::raw('ROUND(SUM(CASE WHEN status = "read" THEN 1 ELSE 0 END) / COUNT(id) * 100, 2) as rate'),
            DB::raw('MAX(created_at) as sent_at')
        )
        ->where('sender_id', $userId)
        ->groupBy('notification_code', 'message', 'target_name', 'target_type')
        ->orderBy('sent_at', 'desc')
        ->get();

    return view('admin.sendMail', compact('notifications'));
}


public function fetchNotifications()
{
    $userId = Auth::id();

    Log::info("Fetching notifications for user ID: " . $userId);

    $notifications = NotificationModel::where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

    Log::info("Found notifications: " . $notifications->count());

    return response()->json($notifications);
}

public function markAllNotificationsRead()
{
    $userId = auth()->id();

    NotificationModel::where('user_id', $userId)
        ->update(['read' => true]);

    return response()->json(['success' => true]);
}
}
