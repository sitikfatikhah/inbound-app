<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $casts = [
        'milestone_date' => 'date',
        'planned_date' => 'date',
        'actual_date' => 'date',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipments::class, 'shipment_id');
    }
}
