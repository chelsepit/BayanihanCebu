<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OnlineDonation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_code',
        'transaction_id',
        'donor_name',
        'donor_email',
        'donor_phone',
        'source_barangay_id',
        'is_anonymous',
        'target_barangay_id',
        'disaster_id',
        'amount',
        'payment_method',
        'payment_proof_url',
        'payment_reference',
        'verification_status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'tx_hash',
        'wallet_address',
        'explorer_url',
        'blockchain_tx_hash',
        'blockchain_status',
        'blockchain_recorded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'verified_at' => 'datetime',
        'blockchain_recorded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($donation) {
            if (empty($donation->tracking_code)) {
                $donation->tracking_code = self::generateTrackingCode();
            }
        });
    }

    public static function generateTrackingCode()
    {
        do {
            $code = 'ON-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (self::where('tracking_code', $code)->exists());

        return $code;
    }

    // Relationships
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'target_barangay_id', 'barangay_id');
    }

    public function disaster()
    {
        return $this->belongsTo(Disaster::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'user_id');
    }

    // Helper Methods
    public function getDonorDisplayName()
    {
        return $this->is_anonymous ? 'Anonymous Donor' : $this->donor_name;
    }

    public function isVerified()
    {
        return $this->verification_status === 'verified';
    }

    public function isBlockchainVerified()
    {
        return $this->blockchain_status === 'confirmed';
    }
}
