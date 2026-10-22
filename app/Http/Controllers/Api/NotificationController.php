<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CustomerNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notifications = CustomerNotification::where('customer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Mark as read
        CustomerNotification::where('customer_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * عدد الإشعارات غير المقروءة
     */
    public function unreadCount()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $count = CustomerNotification::where('customer_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }
}

