<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentMilestones extends Model
{
    protected $fillable = [
        'shipment_id',
        'milestone_name',
        'milestone_date',
        'status',
        'planned_date',
        'actual_date',
        'remarks',
    ];
}
