<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalDeliveryItem extends Model
{
    protected $fillable = [
        'local_delivery_id',
        'purchase_order_item_id',
        'quantity_planned',
        'quantity_dispatched',
        'quantity_received',
        'quantity_rejected',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'quantity_planned' => 'decimal:2',
            'quantity_dispatched' => 'decimal:2',
            'quantity_received' => 'decimal:2',
            'quantity_rejected' => 'decimal:2',
        ];
    }

    public function localDelivery(): BelongsTo
    {
        return $this->belongsTo(LocalDelivery::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(
            PurchaseOrderItems::class,
            'purchase_order_item_id'
        );
    }
}
