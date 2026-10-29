<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocalShipments extends Model
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

    protected $casts = [
        'etd' => 'date',
        'atd' => 'date',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrders::class, 'purchase_orders_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouses::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ShipmentMilestones::class, 'local_shipment_id');
    }

    public function trackingStatus(): HasMany
    {
        return $this->hasMany(TrackingStatus::class, 'name');
    }
}
