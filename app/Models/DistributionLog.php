<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'physical_donation_id',
        'distributed_to',
        'quantity_distributed',
        'distributed_by',
        'distributed_at',
        'notes',
        'photo_urls',
    ];

    protected $casts = [
        'photo_urls' => 'array',
        'distributed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship: belongs to a physical donation
    public function physicalDonation()
    {
        return $this->belongsTo(PhysicalDonation::class);
    }

    // Relationship: distributed by a user
    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributed_by', 'user_id');
    }
}