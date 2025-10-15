<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Barangay extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'barangays';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'barangay_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'barangay_id',
        'name',
        'slug',
        'city',
        'district',
        'latitude',
        'longitude',
        'status',
        'disaster_status',
        'description',
        'contact_person',
        'contact_phone',
        'contact_email',
        'affected_families',
        'needs_summary',
        'blockchain_address',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'affected_families' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($barangay) {
            // Auto-generate barangay_id if not provided
            if (empty($barangay->barangay_id)) {
                $barangay->barangay_id = 'BRG' . str_pad(rand(1, 9999999), 7, '0', STR_PAD_LEFT);
            }
            
            // Auto-generate slug if not provided
            if (empty($barangay->slug)) {
                $barangay->slug = Str::slug($barangay->name);
            }
        });
    }

    /**
     * Get all disasters for this barangay
     */
    public function disasters()
    {
        return $this->hasMany(Disaster::class, 'barangay_id', 'barangay_id');
    }

    /**
     * Get active disasters for this barangay
     */
    public function activeDisasters()
    {
        return $this->hasMany(Disaster::class, 'barangay_id', 'barangay_id')->where('is_active', true);
    }

    /**
     * Get the current active disaster (most recent)
     */
    public function currentDisaster()
    {
        return $this->hasOne(Disaster::class, 'barangay_id', 'barangay_id')
            ->where('is_active', true)
            ->latest();
    }

    /**
     * Get general resource needs for this barangay (non-disaster specific)
     */
    public function resourceNeeds()
    {
        return $this->hasMany(ResourceNeed::class, 'barangay_id', 'barangay_id');
    }

    /**
     * Get pending resource needs
     */
    public function pendingResourceNeeds()
    {
        return $this->hasMany(ResourceNeed::class, 'barangay_id', 'barangay_id')->where('status', 'pending');
    }

    /**
     * Check if barangay has any active disasters
     */
    public function hasActiveDisaster()
    {
        return $this->activeDisasters()->exists();
    }

    /**
     * Get total affected families across all active disasters
     */
    public function getTotalAffectedFamiliesAttribute()
    {
        return $this->activeDisasters()->sum('affected_families');
    }

    /**
     * Get total donations across all active disasters
     */
    public function getTotalDonationsAttribute()
    {
        return $this->activeDisasters()->sum('total_donations');
    }

    /**
     * Update barangay status based on active disasters
     */
    public function updateStatus()
    {
        $activeDisaster = $this->currentDisaster()->first();
        
        if (!$activeDisaster) {
            $this->update(['status' => 'safe', 'disaster_status' => 'safe']);
            return;
        }

        $this->update([
            'status' => $activeDisaster->severity,
            'disaster_status' => $activeDisaster->severity
        ]);
    }

    /**
     * Scope to get barangays with active disasters
     */
    public function scopeWithActiveDisasters($query)
    {
        return $query->whereHas('activeDisasters');
    }

    /**
     * Scope to get safe barangays
     */
    public function scopeSafe($query)
    {
        return $query->where('status', 'safe')
                     ->orWhere('disaster_status', 'safe');
    }
}