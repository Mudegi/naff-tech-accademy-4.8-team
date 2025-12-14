<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        
        // Allow both teachers and students to access notifications
        if (!in_array($user->account_type, ['teacher', 'student'])) {
            abort(403, 'Access denied. Only teachers and students can view notifications.');
        }

        $notifications = Notification::with(['resource', 'comment.user', 'universityCutOff'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('student.notifications', compact('notifications'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for the authenticated user
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notifications count (for AJAX requests)
     */
    public function getUnreadCount()
    {
        $count = Notification::getUnreadCountForUser(Auth::id());
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (for AJAX requests)
     */
    public function getRecent()
    {
        $notifications = Notification::with(['resource', 'comment.user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json($notifications);
    }
}
