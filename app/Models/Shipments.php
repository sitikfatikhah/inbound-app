<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Shipments extends Model
{
    protected $fillable = [
        'shipment_number',
        'purchase_orders_id',
        'forwarder_id',
        'shipping_line_id',
        'port_of_loading_id',
        'port_of_discharge_id',
        'warehouse_id',
        'bl_number',
        'invoice_number',
        'vessel_name',
        'voyage_number',
        'etd',
        'eta',
        'original_etd',
        'original_eta',
        'atd',
        'ata',
        'status_document',
        'tracking_status',
        'remarks',
    ];

    protected $casts = [
        'etd' => 'date',
        'eta' => 'date',
        'original_etd' => 'date',
        'original_eta' => 'date',
        'atd' => 'date',
        'ata' => 'date',
    ];

    public function forwarder(): BelongsTo
    {
        return $this->belongsTo(Forwarders::class, 'forwarder_id');
    }

    public function shippingLine(): BelongsTo
    {
        return $this->belongsTo(ShippingLines::class, 'shipping_line_id');
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
        return $this->belongsToMany(PurchaseOrders::class, 'shipment_purchase_orders', 'shipment_id', 'purchase_order_id')
            ->withTimestamps();
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(PurchaseOrderItems::class);
    }

    public function shipmentContainers(): HasMany
    {
        return $this->hasMany(ShipmentContainers::class, 'shipment_id');
    }

    public function shipmentDocuments(): HasMany
    {
        return $this->hasMany(ShipmentDocuments::class, 'shipment_id');
    }

    public function customsClearances(): HasMany
    {
        return $this->hasMany(CustomsClearance::class, 'shipment_id');
    }

    public function scheduleHistories(): HasMany
    {
        return $this->hasMany(ShipmentScheduleHistories::class, 'shipment_id')->latest();
    }

    public function warehouseReceipts(): HasMany
    {
        return $this->hasMany(WarehouseReceipts::class, 'shipment_id');
    }

}
