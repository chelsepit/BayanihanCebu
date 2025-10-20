<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceNeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_id',
        'category',
        'description',
        'quantity',
        'urgency',
        'status',
        'verification_status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'blockchain_tx_hash',
        'blockchain_status',
        'blockchain_recorded_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'blockchain_recorded_at' => 'datetime',
    ];

   
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id', 'barangay_id');
    }


    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'user_id');
    }


    public function scopePendingVerification($query)
    {
        return $query->where('verification_status', 'pending');
    }


    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }


    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'rejected');
    }
}