<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentContainers extends Model
{
    protected $fillable = [
        'shipment_id',
        'purchase_order_item_id',
        'product_code',
        'quantity',
        'container_number',
        'container_size',
        'seal_number',
        'container_type',
        'gross_weight',
        'net_weight',
        'cbm',
        'status',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipments::class, 'shipment_id');
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItems::class, 'purchase_order_item_id');
    }
}
