<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrders extends Model
{
    protected $fillable = [
        'po_number',
        'supplier_id',
        'category', // import / local
        'shipping_line_id',
        'po_date',
        'currency',
        'exchange_rate',
        'incoterm',
        'payment_term',
        'delivery_date',
        'arrival_date',
        'status',
    ];

    protected $casts = [
        'po_date' => 'datetime',
        'delivery_date' => 'datetime',
        'arrival_date' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Suppliers::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItems::class, 'purchase_order_id');
    }

    public function shipments(): BelongsToMany
    {
        return $this->belongsToMany(Shipments::class, 'shipment_purchase_orders', 'purchase_order_id', 'shipment_id')
            ->withTimestamps();
    }

    public function localDeliveries(): HasMany
    {
        return $this->hasMany(LocalDelivery::class, 'purchase_order_id');
    }
}
