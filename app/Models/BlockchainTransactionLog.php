<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockchainTransactionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tx_hash',
        'transaction_type',
        'reference_id',
        'from_address',
        'to_address',
        'contract_address',
        'function_called',
        'gas_used',
        'gas_price',
        'block_number',
        'status',
        'error_message',
        'retry_count',
        'ipfs_hash',
        'sent_at',
        'confirmed_at',
    ];

    protected $casts = [
        'gas_used' => 'decimal:0',
        'gas_price' => 'decimal:0',
        'retry_count' => 'integer',
        'sent_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }
}
