<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_need_id',
        'requesting_barangay_id',
        'physical_donation_id',
        'donating_barangay_id',
        'match_score',
        'quantity_requested',
        'can_fully_fulfill',
        'status',
        'initiated_by',
        'initiated_at',
        'responded_by',
        'responded_at',
        'response_message',
        'completed_at',
        'completion_notes',
    ];

    protected $casts = [
        'can_fully_fulfill' => 'boolean',
        'match_score' => 'decimal:2',
        'initiated_at' => 'datetime',
        'responded_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * The resource need that is being matched
     */
    public function resourceNeed()
    {
        return $this->belongsTo(ResourceNeed::class, 'resource_need_id');
    }

    /**
     * The physical donation that can fulfill the need
     */
    public function physicalDonation()
    {
        return $this->belongsTo(PhysicalDonation::class, 'physical_donation_id');
    }

    /**
     * The barangay requesting resources
     */
    public function requestingBarangay()
    {
        return $this->belongsTo(Barangay::class, 'requesting_barangay_id', 'barangay_id');
    }

    /**
     * The barangay donating resources
     */
    public function donatingBarangay()
    {
        return $this->belongsTo(Barangay::class, 'donating_barangay_id', 'barangay_id');
    }

    /**
     * The LDRRMO user who initiated the match
     */
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by', 'user_id');
    }

    /**
     * The BDRRMC user who responded (accepted/rejected)
     */
    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by', 'user_id');
    }

    /**
     * Notifications related to this match
     */
    public function notifications()
    {
        return $this->hasMany(MatchNotification::class, 'resource_match_id');
    }

    /**
     * Conversation for this match (if accepted)
     */
    public function conversation()
    {
        return $this->hasOne(MatchConversation::class, 'resource_match_id');
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope for pending matches (awaiting donor response)
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for accepted matches
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope for rejected matches
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for completed matches
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for cancelled matches
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for matches involving a specific barangay
     */
    public function scopeForBarangay($query, $barangayId)
    {
        return $query->where(function($q) use ($barangayId) {
            $q->where('requesting_barangay_id', $barangayId)
              ->orWhere('donating_barangay_id', $barangayId);
        });
    }

    /**
     * Scope for matches where barangay is the donor
     */
    public function scopeAsDonor($query, $barangayId)
    {
        return $query->where('donating_barangay_id', $barangayId);
    }

    /**
     * Scope for matches where barangay is the requester
     */
    public function scopeAsRequester($query, $barangayId)
    {
        return $query->where('requesting_barangay_id', $barangayId);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Check if match is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if match is accepted
     */
    public function isAccepted()
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if match is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if match can be responded to
     */
    public function canRespond()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if conversation is available
     */
    public function hasConversation()
    {
        return $this->conversation !== null;
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'yellow',
            'accepted' => 'green',
            'rejected' => 'red',
            'completed' => 'blue',
            'cancelled' => 'gray',
        ][$this->status] ?? 'gray';
    }

    /**
     * Get status label for UI
     */
    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Waiting for Response',
            'accepted' => 'Accepted - Active',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ][$this->status] ?? $this->status;
    }
}