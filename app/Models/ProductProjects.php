<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductProjects extends Model
{
    protected $fillable = [
        'product_id',
        'project_name',
        'description',
        'start_date',
        'target_date',
        'pic',
        'progress',
        'shipment_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipments::class);
    }
}
