<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentContainers extends Model
{
    protected $fillable = [
        'shipment_id',
        'container_number',
        'container_size',
        'seal_number',
        'gross_weight',
        'net_weight',
        'cbm',
        'status',
    ];
}
