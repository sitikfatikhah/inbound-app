<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentLocations extends Model
{
    protected $fillable = [
        'shipment_id',
        'latitude',
        'longitude',
        'location_name',
        'event_time',
        'status',
    ];
}
