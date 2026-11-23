<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomsClearance extends Model
{
    protected $fillable = [
        'shipment_id',
        'pib_number',
        'pib_date',
        'penjaluran',
        'customs_status',
        'attachment_path',
        'inspection_start',
        'inspection_end',
        'inspection_duration',
        'remarks',
    ];

    protected $casts = [
        'pib_date' => 'date',
        'inspection_start' => 'datetime',
        'inspection_end' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipments::class, 'shipment_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(
            CustomsClearanceResponses::class,
            'customs_clearance_id'
        );
    }
}
