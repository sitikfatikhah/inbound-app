<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class LocalShipments extends Model
{
    protected $fillable = [
        'shipment_number',
        'purchase_order_id',
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
        return $this->belongsTo(PurchaseOrders::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouses::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(LocalShipmentMilestone::class, 'local_shipment_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(LocalShipmentStatusHistory::class, 'local_shipment_id')
            ->orderBy('status_date')
            ->orderBy('id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LocalShipmentItem::class, 'local_shipment_id');
    }

    public function purchaseOrders(): HasManyThrough
    {
        return $this->hasManyThrough(
            PurchaseOrders::class,
            LocalShipmentItem::class,
            'local_shipment_id',
            'id',
            'id',
            'purchase_order_id',
        )->select('purchase_orders.*')->distinct();
    }

    public function trackingStatus(): BelongsTo
    {
        return $this->belongsTo(TrackingStatus::class, 'tracking_status', 'name')
            ->where('category', 'shipment');
    }
}
