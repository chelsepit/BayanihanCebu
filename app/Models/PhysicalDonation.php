<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PhysicalDonation extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_id',
        'tracking_code',
        'donor_name',
        'donor_contact',
        'donor_email',
        'donor_address',
        'category',
        'items_description',
        'quantity',
        'estimated_value',
        'photo_urls',
        'intended_recipients',
        'notes',
        'distribution_status',
        'recorded_by',
        'recorded_at',
    ];

    protected $casts = [
        'photo_urls' => 'array',
        'estimated_value' => 'decimal:2',
        'recorded_at' => 'datetime',
        'blockchain_recorded_at' => 'datetime',
        'verified_at' => 'datetime',
        'last_verification_check' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['explorer_url'];


    // Relationship: belongs to a barangay
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'barangay_id');
    }

    // Relationship: recorded by a user
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by', 'user_id');
    }

    // Relationship: has many distribution logs
    public function distributions()
    {
        return $this->hasMany(DistributionLog::class);
    }

    // Generate unique tracking code
    public static function generateTrackingCode($barangayId)
    {
        // Format: BRG-YEAR-SEQUENTIAL (e.g., LAH-2025-00001)
        $year = date('Y');
        $lastDonation = self::where('barangay_id', $barangayId)
            ->where('tracking_code', 'like', $barangayId . '-' . $year . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastDonation) {
            $lastNumber = (int) substr($lastDonation->tracking_code, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return strtoupper($barangayId) . '-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate and store offchain hash of items description
     */
    public function generateOffchainHash(): void
    {
        $blockchainService = app(\App\Services\PhysicalDonationBlockchainService::class);
        $this->offchain_hash = $blockchainService->generateOffchainHash($this);
        $this->verification_status = 'unverified';
        $this->save();
    }

    /**
     * Record this donation to blockchain
     */
    public function recordToBlockchain(): array
    {
        $blockchainService = app(\App\Services\PhysicalDonationBlockchainService::class);
        return $blockchainService->recordToBlockchain($this);
    }

    /**
     * Verify blockchain integrity by comparing hashes
     */
    public function verifyBlockchainIntegrity(): array
    {
        $blockchainService = app(\App\Services\PhysicalDonationBlockchainService::class);
        return $blockchainService->verifyDonation($this);
    }

    /**
     * Check if donation is verified
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    /**
     * Check if there's a hash mismatch
     */
    public function hasMismatch(): bool
    {
        return $this->verification_status === 'mismatch';
    }

    /**
     * Get verification status badge color
     */
    public function getVerificationBadgeColor(): string
    {
        return match($this->verification_status) {
            'verified' => 'green',
            'mismatch' => 'red',
            'unverified' => 'yellow',
            default => 'gray'
        };
    }
/**
 * Get the blockchain explorer URL
 */
public function getExplorerUrlAttribute(): ?string
{
    if (!$this->blockchain_tx_hash) {
        return null;
    }
    
    return "https://sepolia-blockscout.lisk.com/tx/{$this->blockchain_tx_hash}";
}
    /**
     * Get verification status label
     */
public function getVerificationStatusLabel(): string
{
    if (!$this->offchain_hash) {
        return 'Not Recorded';
    }

    if (!$this->onchain_hash) {
        return 'Pending Verification';
    }

    return match($this->verification_status) {
        'verified' => 'Verified',
        'mismatch' => 'Mismatch Detected',
        default => 'Pending'
    };
}
}