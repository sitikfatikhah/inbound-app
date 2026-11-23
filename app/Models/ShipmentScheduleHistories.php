<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentScheduleHistories extends Model
{
    protected $fillable = [
        'shipment_id',
        'schedule_type',
        'previous_date',
        'new_date',
        'revision_reason',
        'source',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'previous_date' => 'date',
            'new_date' => 'date',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipments::class, 'shipment_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
