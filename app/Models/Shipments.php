<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipments extends Model
{
    protected $fillable = [
        'company_id',
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
        'etd_actual',
        'eta_actual',
        'ata',
        'shipment_number', //automatically generated
        'shipment_date',
        'status',
        'tracking_status',
    ];
}
