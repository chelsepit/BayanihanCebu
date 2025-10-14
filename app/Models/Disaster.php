<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_id',
        'title',
        'description',
        'type',
        'severity',
        'affected_families',
        'total_donations',
        'is_active',
        'started_at',
        'resolved_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
        'affected_families' => 'integer',
        'total_donations' => 'decimal:2',
    ];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function urgentNeeds()
    {
        return $this->hasMany(UrgentNeed::class);
    }

    public function activeUrgentNeeds()
    {
        return $this->hasMany(UrgentNeed::class)->where('is_fulfilled', false);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function confirmedDonations()
    {
        return $this->hasMany(Donation::class)->whereIn('status', ['confirmed', 'distributed', 'completed']);
    }

    public function addDonation($amount)
    {
        $this->increment('total_donations', $amount);
        $this->barangay->updateStatus();
    }

    public function resolve()
    {
        $this->update([
            'is_active' => false,
            'resolved_at' => now(),
        ]);
        
        $this->barangay->updateStatus();
    }

    public function reactivate()
    {
        $this->update([
            'is_active' => true,
            'resolved_at' => null,
        ]);
        
        $this->barangay->updateStatus();
    }

    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'warning' => 'yellow',
            'critical' => 'orange',
            'emergency' => 'red',
            default => 'gray',
        };
    }

    public function getTypeDisplayAttribute()
    {
        return ucfirst($this->type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeResolved($query)
    {
        return $query->where('is_active', false);
    }
}