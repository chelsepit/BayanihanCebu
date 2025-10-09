<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;

    protected $primaryKey = 'barangay_id';
    public $incrementing = false; // since barangay_id is a string, not an auto-increment integer
    protected $keyType = 'string';

    protected $fillable = [
        'barangay_id',
        'name',
        'city',
        'latitude',
        'longitude',
        'disaster_status',
        'needs_summary',
        'blockchain_address',
    ];

    // Optional: relationship if each barangay has users
    public function users()
    {
        return $this->hasMany(User::class, 'barangay_id', 'barangay_id');
    }
}
