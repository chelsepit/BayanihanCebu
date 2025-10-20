<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_match_id',
        'requesting_barangay_id',
        'donating_barangay_id',
        'is_active',
        'last_message_at',
        'last_message_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * The resource match this conversation belongs to
     */
    public function resourceMatch()
    {
        return $this->belongsTo(ResourceMatch::class, 'resource_match_id');
    }

    /**
     * The requesting barangay
     */
    public function requestingBarangay()
    {
        return $this->belongsTo(Barangay::class, 'requesting_barangay_id', 'barangay_id');
    }

    /**
     * The donating barangay
     */
    public function donatingBarangay()
    {
        return $this->belongsTo(Barangay::class, 'donating_barangay_id', 'barangay_id');
    }

    /**
     * All messages in this conversation
     */
    public function messages()
    {
        return $this->hasMany(MatchMessage::class, 'conversation_id');
    }

    /**
     * Unread messages for requester
     */
    public function unreadForRequester()
    {
        return $this->hasMany(MatchMessage::class, 'conversation_id')
            ->where('is_read_by_requester', false)
            ->where('sender_barangay_id', '!=', $this->requesting_barangay_id);
    }

    /**
     * Unread messages for donor
     */
    public function unreadForDonor()
    {
        return $this->hasMany(MatchMessage::class, 'conversation_id')
            ->where('is_read_by_donor', false)
            ->where('sender_barangay_id', '!=', $this->donating_barangay_id);
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope for active conversations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for conversations involving a specific barangay
     */
    public function scopeForBarangay($query, $barangayId)
    {
        return $query->where(function($q) use ($barangayId) {
            $q->where('requesting_barangay_id', $barangayId)
              ->orWhere('donating_barangay_id', $barangayId);
        });
    }

    /**
     * Scope for conversations with recent activity
     */
    public function scopeRecentlyActive($query, $hours = 24)
    {
        return $query->where('last_message_at', '>=', now()->subHours($hours));
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Get unread count for a specific barangay
     */
    public function getUnreadCountFor($barangayId)
    {
        if ($barangayId === $this->requesting_barangay_id) {
            return $this->unreadForRequester()->count();
        } elseif ($barangayId === $this->donating_barangay_id) {
            return $this->unreadForDonor()->count();
        }
        return 0;
    }

    /**
     * Mark all messages as read for a specific barangay
     */
    public function markAsReadFor($barangayId)
    {
        if ($barangayId === $this->requesting_barangay_id) {
            $this->messages()
                ->where('sender_barangay_id', '!=', $barangayId)
                ->update(['is_read_by_requester' => true]);
        } elseif ($barangayId === $this->donating_barangay_id) {
            $this->messages()
                ->where('sender_barangay_id', '!=', $barangayId)
                ->update(['is_read_by_donor' => true]);
        }
    }

    /**
     * Add a new message to the conversation
     */
    public function addMessage($senderUserId, $senderBarangayId, $message, $type = 'text', $attachmentData = null)
    {
        $messageData = [
            'sender_user_id' => $senderUserId,
            'sender_barangay_id' => $senderBarangayId,
            'message' => $message,
            'message_type' => $type,
        ];

        if ($attachmentData) {
            $messageData['attachment_url'] = $attachmentData['url'] ?? null;
            $messageData['attachment_type'] = $attachmentData['type'] ?? null;
            $messageData['attachment_name'] = $attachmentData['name'] ?? null;
        }

        $newMessage = $this->messages()->create($messageData);

        // Update last message info
        $this->update([
            'last_message_at' => now(),
            'last_message_by' => $senderBarangayId,
        ]);

        return $newMessage;
    }

    /**
     * Add a system message
     */
    public function addSystemMessage($message)
    {
        return $this->messages()->create([
            'sender_user_id' => 'SYSTEM',
            'sender_barangay_id' => 'SYSTEM',
            'message' => $message,
            'message_type' => 'system',
            'is_read_by_requester' => true,
            'is_read_by_donor' => true,
        ]);
    }

    /**
     * Close/deactivate the conversation
     */
    public function close()
    {
        $this->update(['is_active' => false]);
        $this->addSystemMessage('This conversation has been closed. Transfer completed.');
    }

    /**
     * Get the other participant's barangay
     */
    public function getOtherParticipant($currentBarangayId)
    {
        if ($currentBarangayId === $this->requesting_barangay_id) {
            return $this->donatingBarangay;
        } elseif ($currentBarangayId === $this->donating_barangay_id) {
            return $this->requestingBarangay;
        }
        return null;
    }

    /**
     * Check if barangay is participant
     */
    public function isParticipant($barangayId)
    {
        return $barangayId === $this->requesting_barangay_id || 
               $barangayId === $this->donating_barangay_id;
    }

    /**
     * Get conversation title
     */
    public function getTitleAttribute()
    {
        $need = $this->resourceMatch->resourceNeed;
        return "Match: {$need->category} Transfer";
    }
}