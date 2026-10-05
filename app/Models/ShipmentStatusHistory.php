<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentStatusHistory extends Model
{
    protected $fillable = [
        'shipment_id',
        'status',
        'status_date',
        'location',
        'country',
        'remarks',
    ];

    protected $casts = [
        'status_date' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipments::class);
    }
}
