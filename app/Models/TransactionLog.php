<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'online_donation_id',
        'action',
        'details',
        'created_at'
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    public function onlineDonation()
    {
        return $this->belongsTo(OnlineDonation::class);
    }
}
