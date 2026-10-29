<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrderItems extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'product_code',
        'product_name',
        'hs_code',
        'quantity',
        'backorder_qty',
        'remaining_qty',
        'total_allocated',
        'unit_price',
        'unit',
        'facility',
        'import_duty',
        'exchange_rate',
        'total_price',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrders::class);
    }

    public function getRemainingQuantityAttribute(): float
    {
        return max(
            0,
            (float) ($this->getAttribute('remaining_qty') ?? $this->getAttribute('quantity') ?? 0)
        );
    }

    public function getRemainingQuantityFormattedAttribute(): string
    {
        return number_format($this->remaining_quantity, 2);
    }

    public function shipmentAllocations(): HasMany
    {
        return $this->hasMany(ShipmentContainers::class, 'purchase_order_item_id');
    }
}
