<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
}