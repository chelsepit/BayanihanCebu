<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\MatchNotification;

class NotificationController extends Controller
{
    /**
     * Get all notifications for current user's barangay
     */
    public function getNotifications(Request $request)
    {
        try {
            $barangayId = session('barangay_id');
            $limit = $request->query('limit', 50);
            $type = $request->query('type', 'all');

            $query = MatchNotification::with([
                'resourceMatch.resourceNeed',
                'resourceMatch.requestingBarangay',
                'resourceMatch.donatingBarangay'
            ])
            ->forBarangay($barangayId)
            ->orderBy('created_at', 'desc')
            ->limit($limit);

            // Filter by type if specified
            if ($type !== 'all') {
                $query->ofType($type);
            }

            $notifications = $query->get()->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'is_read' => $notification->is_read,
                    'requires_action' => $notification->requiresAction(),
                    'match_id' => $notification->resource_match_id,
                    'match_status' => $notification->resourceMatch->status ?? null,
                    'created_at' => $notification->created_at->format('M d, Y h:i A'),
                    'time_ago' => $notification->time_ago,
                ];
            });

            return response()->json($notifications);

        } catch (\Exception $e) {
            Log::error('Error loading notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        try {
            $barangayId = session('barangay_id');

            $count = MatchNotification::forBarangay($barangayId)
                ->unread()
                ->count();

            return response()->json([
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting unread count',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($notificationId)
    {
        try {
            $barangayId = session('barangay_id');

            $notification = MatchNotification::where('id', $notificationId)
                ->where('barangay_id', $barangayId)
                ->firstOrFail();

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error marking notification as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $barangayId = session('barangay_id');

            MatchNotification::forBarangay($barangayId)
                ->unread()
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error marking notifications as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a notification
     */
    public function deleteNotification($notificationId)
    {
        try {
            $barangayId = session('barangay_id');

            $notification = MatchNotification::where('id', $notificationId)
                ->where('barangay_id', $barangayId)
                ->firstOrFail();

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notifications grouped by type
     */
    public function getGroupedNotifications()
    {
        try {
            $barangayId = session('barangay_id');

            $notifications = MatchNotification::forBarangay($barangayId)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->groupBy('type');

            $grouped = $notifications->map(function($group, $type) {
                return [
                    'type' => $type,
                    'count' => $group->count(),
                    'unread_count' => $group->where('is_read', false)->count(),
                    'notifications' => $group->take(10)->map(function($n) {
                        return [
                            'id' => $n->id,
                            'title' => $n->title,
                            'message' => $n->message,
                            'is_read' => $n->is_read,
                            'time_ago' => $n->time_ago,
                        ];
                    })->values(),
                ];
            })->values();

            return response()->json($grouped);

        } catch (\Exception $e) {
            Log::error('Error loading grouped notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}