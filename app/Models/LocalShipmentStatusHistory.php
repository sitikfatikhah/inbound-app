<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalShipmentStatusHistory extends Model
{
    protected $fillable = [
        'local_shipment_id',
        'status_type',
        'status',
        'status_date',
    ];

    protected function casts(): array
    {
        return [
            'status_date' => 'datetime',
        ];
    }

    public function localShipment(): BelongsTo
    {
        return $this->belongsTo(LocalShipments::class, 'local_shipment_id');
    }
}
