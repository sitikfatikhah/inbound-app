<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseReceipts extends Model
{
    protected $fillable = [
        'shipment_id',
        'warehouse_id',
        'arrival_number',
        'received_date',
        'status',
    ];
}
