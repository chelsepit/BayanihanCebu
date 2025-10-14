<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrgentNeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'disaster_id',
        'type',
        'quantity_needed',
        'unit',
        'quantity_fulfilled',
        'is_fulfilled',
    ];

    protected $casts = [
        'is_fulfilled' => 'boolean',
        'quantity_needed' => 'integer',
        'quantity_fulfilled' => 'integer',
    ];

    public function disaster()
    {
        return $this->belongsTo(Disaster::class);
    }

    public function getTypeDisplayAttribute()
    {
        return ucfirst($this->type);
    }

    public function getFulfillmentPercentageAttribute()
    {
        if (!$this->quantity_needed || $this->quantity_needed == 0) {
            return 0;
        }

        return min(100, ($this->quantity_fulfilled / $this->quantity_needed) * 100);
    }

    public function checkFulfillment()
    {
        if ($this->quantity_needed && $this->quantity_fulfilled >= $this->quantity_needed) {
            $this->update(['is_fulfilled' => true]);
        }
    }

    public function addFulfilled($quantity)
    {
        $this->increment('quantity_fulfilled', $quantity);
        $this->checkFulfillment();
    }

    public function scopeUnfulfilled($query)
    {
        return $query->where('is_fulfilled', false);
    }

    public function scopeFulfilled($query)
    {
        return $query->where('is_fulfilled', true);
    }
}