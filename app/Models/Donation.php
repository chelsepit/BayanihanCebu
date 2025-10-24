<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_id',
        'user_id',
        'tracking_code',
        'amount',
        'donation_type',
        'items',
        'status',
        'transaction_hash',
        'paymongo_payment_id',
        'payment_source_id',
        'payment_session_id',
        'payment_id',
        'checkout_url',
        'paymongo_payment_intent_id',
        'payment_method',
        'payment_status',
        'paid_at',
        'donor_name',
        'donor_email',
        'donor_phone',
        'is_anonymous',
        'distributed_at',
        'distribution_notes',
        // Blockchain recording fields (DonationRecorder smart contract)
        'blockchain_tx_hash',
        'blockchain_status',
        'blockchain_recorded_at',
        'explorer_url',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'distributed_at' => 'datetime',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'blockchain_recorded_at' => 'datetime',
        'items' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($donation) {
            if (empty($donation->tracking_code)) {
                $donation->tracking_code = static::generateTrackingCode();
            }
        });
    }

    public static function generateTrackingCode()
    {
        do {
            $code = 'DON-' . strtoupper(Str::random(8));
        } while (static::where('tracking_code', $code)->exists());

        return $code;
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'barangay_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function getDonorDisplayNameAttribute()
    {
        if ($this->is_anonymous) {
            return 'Anonymous Donor';
        }

        return $this->donor_name ?? $this->user?->full_name ?? 'Unknown Donor';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'distributed' => 'green',
            'completed' => 'green',
            default => 'gray',
        };
    }

    public function isBlockchainVerified()
    {
        return $this->blockchain_status === 'confirmed';
    }

    public function confirm($transactionHash = null)
    {
        $this->update([
            'status' => 'confirmed',
            'transaction_hash' => $transactionHash,
        ]);
    }

    public function markAsDistributed($notes = null)
    {
        $this->update([
            'status' => 'distributed',
            'distributed_at' => now(),
            'distribution_notes' => $notes,
        ]);
    }

    public function complete($notes = null)
    {
        $this->update([
            'status' => 'completed',
            'distribution_notes' => $notes,
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeDistributed($query)
    {
        return $query->whereIn('status', ['distributed', 'completed']);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
