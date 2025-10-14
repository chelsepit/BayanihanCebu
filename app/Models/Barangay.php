<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Barangay extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'latitude',
        'longitude',
        'status',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($barangay) {
            if (empty($barangay->slug)) {
                $barangay->slug = Str::slug($barangay->name);
            }
        });
    }

    public function disasters()
    {
        return $this->hasMany(Disaster::class);
    }

    public function activeDisasters()
    {
        return $this->hasMany(Disaster::class)->where('is_active', true);
    }

    public function currentDisaster()
    {
        return $this->hasOne(Disaster::class)
            ->where('is_active', true)
            ->latest();
    }

    public function hasActiveDisaster()
    {
        return $this->activeDisasters()->exists();
    }

    public function getTotalAffectedFamiliesAttribute()
    {
        return $this->activeDisasters()->sum('affected_families');
    }

    public function getTotalDonationsAttribute()
    {
        return $this->activeDisasters()->sum('total_donations');
    }

    public function updateStatus()
    {
        $activeDisaster = $this->currentDisaster()->first();
        
        if (!$activeDisaster) {
            $this->update(['status' => 'safe']);
            return;
        }

        $this->update(['status' => $activeDisaster->severity]);
    }

    public function scopeWithActiveDisasters($query)
    {
        return $query->whereHas('activeDisasters');
    }

    public function scopeSafe($query)
    {
        return $query->where('status', 'safe');
    }
}