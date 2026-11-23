<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentCustomsClearances extends Model
{
    protected $fillable = [
        'shipment_id',
        'jalur',
        'pib_number',
        'pib_date',
        'sppb_number',
        'sppb_date',
        'inspection_start',
        'inspection_end',
        'inspection_duration',
        'remarks',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'pib_date' => 'date',
            'sppb_date' => 'date',
            'inspection_start' => 'datetime',
            'inspection_end' => 'datetime',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipments::class, 'shipment_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
