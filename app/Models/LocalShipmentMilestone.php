<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalShipmentMilestone extends Model
{
    protected $fillable = [
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

    public function localShipment(): BelongsTo
    {
        return $this->belongsTo(LocalShipments::class, 'local_shipment_id');
    }
}
