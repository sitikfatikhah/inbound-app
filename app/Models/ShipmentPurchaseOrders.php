<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentPurchaseOrders extends Model
{
    protected $fillable = [
        'shipment_id',
        'purchase_order_id',
    ];
}
