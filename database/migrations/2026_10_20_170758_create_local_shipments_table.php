<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalShipment extends Model
{
    protected $fillable = [
        'shipment_number',
        'purchase_orders_id',
        'warehouse_id',
        'delivery_order_number',
        'etd',
        'atd',
        'status_document',
        'tracking_status',
        'remarks',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(
            PurchaseOrders::class,
            'purchase_order_id',
            'id'
        );
    }
}
