<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipments extends Model
{
    protected $fillable = [
        'shipment_number',
        'forwarder_id',
        'shipping_line_id',
        'port_of_loading_id',
        'port_of_discharge_id',
        'warehouse_id',
        'bl_number',
        'vessel_name',
        'voyage_number',
        'etd',
        'eta',
        'atd',
        'ata',
        'status_document',
        'tracking_status',
        'remarks',
    ];

    protected $casts = [
        'etd' => 'date',
        'eta' => 'date',
        'atd' => 'date',
        'ata' => 'date',
    ];

    public function forwarder(): BelongsTo
    {
        return $this->belongsTo(Forwarders::class);
    }

    public function shippingLine(): BelongsTo
    {
        return $this->belongsTo(ShippingLines::class);
    }

    public function portOfLoading(): BelongsTo
    {
        return $this->belongsTo(Ports::class, 'port_of_loading_id');
    }

    public function portOfDischarge(): BelongsTo
    {
        return $this->belongsTo(Ports::class, 'port_of_discharge_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouses::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ShipmentMilestones::class, 'shipment_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ShipmentStatusHistory::class, 'shipment_id')
            ->latest('status_date');
    }

    public function purchaseOrders(): BelongsToMany
    {
        return $this->belongsToMany(PurchaseOrders::class, 'shipment_purchase_orders', 'shipment_id', 'purchase_order_id');
    }
}
