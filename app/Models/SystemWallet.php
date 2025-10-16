<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'wallet_address',
        'network',
        'purpose',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function getMainWallet()
    {
        return self::where('purpose', 'receiving')
            ->where('is_active', true)
            ->first();
    }
}
