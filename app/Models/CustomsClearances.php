<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomsClearances extends Model
{
    protected $fillable = [
        'shipment_id',
        'pib_number',
        'pib_date',
        'nopen',
        'sppb_number',
        'sppb_date',
        'routing_status',
        'inspection_status',
        'inspection_date',
        'customs_clearance_date',
        'customs_clearance_status',
        'remarks',
    ];
}
