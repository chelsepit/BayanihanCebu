<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_match_id',
        'barangay_id',
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * The resource match this notification is about
     */
    public function resourceMatch()
    {
        return $this->belongsTo(ResourceMatch::class, 'resource_match_id');
    }

    /**
     * The barangay this notification is for
     */
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'barangay_id');
    }

    /**
     * The specific user this notification is for (optional)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for notifications for a specific barangay
     */
    public function scopeForBarangay($query, $barangayId)
    {
        return $query->where('barangay_id', $barangayId);
    }

    /**
     * Scope for notifications for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific notification type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for recent notifications (last 7 days)
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Get icon for notification type
     */
    public function getIconAttribute()
    {
        return [
            'match_request' => 'fa-handshake',
            'match_accepted' => 'fa-check-circle',
            'match_rejected' => 'fa-times-circle',
            'match_completed' => 'fa-flag-checkered',
            'match_cancelled' => 'fa-ban',
            'new_message' => 'fa-envelope',
        ][$this->type] ?? 'fa-bell';
    }

    /**
     * Get color for notification type
     */
    public function getColorAttribute()
    {
        return [
            'match_request' => 'blue',
            'match_accepted' => 'green',
            'match_rejected' => 'red',
            'match_completed' => 'purple',
            'match_cancelled' => 'gray',
            'new_message' => 'yellow',
        ][$this->type] ?? 'gray';
    }

    /**
     * Check if this notification requires action
     */
    public function requiresAction()
    {
        return $this->type === 'match_request' && !$this->is_read;
    }

    /**
     * Get formatted time ago
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}