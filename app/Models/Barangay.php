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
        'disaster_type',
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
     * Get donations for this barangay
     */
    public function donations()
    {
        return $this->hasMany(Donation::class, 'barangay_id', 'barangay_id');
    }

    /**
     * Get general resource needs for this barangay
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
     * Get total raised from donations
     */
    public function getTotalRaisedAttribute()
    {
        return $this->donations()
            ->whereIn('status', ['confirmed', 'distributed', 'completed'])
            ->sum('amount');
    }

    /**
     * Check if barangay needs help
     */
    public function needsHelp()
    {
        return $this->disaster_status !== 'safe';
    }

    /**
     * Scope to get barangays that need help
     */
    public function scopeNeedsHelp($query)
    {
        return $query->where('disaster_status', '!=', 'safe');
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