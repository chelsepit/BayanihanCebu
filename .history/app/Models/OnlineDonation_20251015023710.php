<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineDonation extends Model
{
    protected $fillable = [
        'donor_name',
        'donor_email',
        'source_barangay_id',
        'target_barangay_id',
        'amount',
        'payment_method',
        'tx_hash',
        'blockchain_status',
        'explorer_url',
        'message'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sourceBarangay()
    {
        return $this->belongsTo(Barangay::class, 'source_barangay_id', 'barangay_id');
    }

    public function targetBarangay()
    {
        return $this->belongsTo(Barangay::class, 'target_barangay_id', 'barangay_id');
    }

    public function transactionLogs()
    {
        return $this->hasMany(TransactionLog::class);
    }
}
