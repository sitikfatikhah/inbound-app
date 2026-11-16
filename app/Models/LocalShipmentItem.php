<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalShipmentItem extends Model
{
    protected $fillable = [
        'local_shipment_id',
        'purchase_order_id',
        'purchase_order_item_id',
        'quantity',
    ];

    public function localShipment(): BelongsTo
    {
        return $this->belongsTo(LocalShipments::class, 'local_shipment_id');
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrders::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItems::class);
    }
}
