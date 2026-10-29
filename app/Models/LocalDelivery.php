<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocalDelivery extends Model
{
    protected $fillable = [
        'delivery_number',
        'purchase_order_id',
        'promised_date',
        'ETD',
        'ETA',
        'vehicle_number',
        'vehicle_type',
        'driver_name',
        'driver_phone',
        'status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'promised_date' => 'date',
            'ETD' => 'datetime',
            'ETA' => 'datetime',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrders::class, 'purchase_order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LocalDeliveryItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(LocalDeliveryStatusHistory::class);
    }
}
