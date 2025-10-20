<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_user_id',
        'sender_barangay_id',
        'message_type',
        'message',
        'attachment_url',
        'attachment_type',
        'attachment_name',
        'is_read_by_requester',
        'is_read_by_donor',
        'read_at',
    ];

    protected $casts = [
        'is_read_by_requester' => 'boolean',
        'is_read_by_donor' => 'boolean',
        'read_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * The conversation this message belongs to
     */
    public function conversation()
    {
        return $this->belongsTo(MatchConversation::class, 'conversation_id');
    }

    /**
     * The user who sent this message
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_user_id', 'user_id');
    }

    /**
     * The barangay that sent this message
     */
    public function senderBarangay()
    {
        return $this->belongsTo(Barangay::class, 'sender_barangay_id', 'barangay_id');
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope for messages in a specific conversation
     */
    public function scopeInConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    /**
     * Scope for unread messages by requester
     */
    public function scopeUnreadByRequester($query)
    {
        return $query->where('is_read_by_requester', false);
    }

    /**
     * Scope for unread messages by donor
     */
    public function scopeUnreadByDonor($query)
    {
        return $query->where('is_read_by_donor', false);
    }

    /**
     * Scope for text messages only
     */
    public function scopeTextOnly($query)
    {
        return $query->where('message_type', 'text');
    }

    /**
     * Scope for system messages
     */
    public function scopeSystemMessages($query)
    {
        return $query->where('message_type', 'system');
    }

    /**
     * Scope for messages with attachments
     */
    public function scopeWithAttachments($query)
    {
        return $query->whereNotNull('attachment_url');
    }

    /**
     * Scope for messages sent by a specific barangay
     */
    public function scopeFromBarangay($query, $barangayId)
    {
        return $query->where('sender_barangay_id', $barangayId);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Check if this is a system message
     */
    public function isSystemMessage()
    {
        return $this->message_type === 'system';
    }

    /**
     * Check if message has attachment
     */
    public function hasAttachment()
    {
        return !empty($this->attachment_url);
    }

    /**
     * Check if message is read by specific barangay
     */
    public function isReadBy($barangayId)
    {
        $conversation = $this->conversation;
        
        if ($barangayId === $conversation->requesting_barangay_id) {
            return $this->is_read_by_requester;
        } elseif ($barangayId === $conversation->donating_barangay_id) {
            return $this->is_read_by_donor;
        }
        
        return false;
    }

    /**
     * Mark as read by specific barangay
     */
    public function markAsReadBy($barangayId)
    {
        $conversation = $this->conversation;
        
        if ($barangayId === $conversation->requesting_barangay_id) {
            $this->update(['is_read_by_requester' => true]);
        } elseif ($barangayId === $conversation->donating_barangay_id) {
            $this->update(['is_read_by_donor' => true]);
        }
        
        // Update read_at timestamp if both have read
        if ($this->is_read_by_requester && $this->is_read_by_donor) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Get sender name for display
     */
    public function getSenderNameAttribute()
    {
        if ($this->isSystemMessage()) {
            return 'System';
        }
        
        return $this->senderBarangay->name ?? 'Unknown';
    }

    /**
     * Get formatted timestamp
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('M d, Y h:i A');
    }

    /**
     * Get time ago for display
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get attachment icon based on type
     */
    public function getAttachmentIconAttribute()
    {
        if (!$this->hasAttachment()) {
            return null;
        }

        $icons = [
            'image' => 'fa-image',
            'document' => 'fa-file-pdf',
            'pdf' => 'fa-file-pdf',
            'word' => 'fa-file-word',
            'excel' => 'fa-file-excel',
        ];

        return $icons[$this->attachment_type] ?? 'fa-file';
    }

    /**
     * Get message preview (truncated)
     */
    public function getPreviewAttribute()
    {
        if ($this->isSystemMessage()) {
            return $this->message;
        }

        return strlen($this->message) > 50 
            ? substr($this->message, 0, 50) . '...' 
            : $this->message;
    }

    /**
     * Format message for display (handle line breaks, etc.)
     */
    public function getFormattedMessageAttribute()
    {
        return nl2br(e($this->message));
    }
}