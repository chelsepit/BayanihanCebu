<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
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
        'city',
        'district',
        'latitude',
        'longitude',
        'disaster_status',
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
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'affected_families' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
