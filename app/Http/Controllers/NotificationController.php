<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = session('user_id');

        if (!Schema::hasTable('user_notifications')) {
            $notifications = collect();
            return view('notifications.index', compact('notifications'));
        }

        UserNotification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $notifications = UserNotification::where('user_id', $userId)
            ->with(['picture', 'order'])
            ->orderByDesc('created_at')
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request)
    {
        $userId = session('user_id');
        $notificationId = (int) $request->notification_id;

        $notification = UserNotification::where('user_id', $userId)
            ->where('id', $notificationId)
            ->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Уведомление не найдено'], 404);
        }

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'unread_count' => UserNotification::where('user_id', $userId)->whereNull('read_at')->count(),
        ]);
    }

    public function markAllAsRead()
    {
        $userId = session('user_id');

        UserNotification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}
