<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItems extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'product_code',
        'product_name',
        'hs_code',
        'quantity',
        'unit',
        'facility',
        'import_duty',
        'exchange_rate',
        'total_price',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class);
    }
}
