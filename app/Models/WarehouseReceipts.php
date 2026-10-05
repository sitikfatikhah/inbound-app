<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseReceipts extends Model
{
    protected $fillable = [
        'shipment_id',
        'warehouse_id',
        'arrival_number',
        'received_date',
        'status',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipments::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouses::class);
    }
}
