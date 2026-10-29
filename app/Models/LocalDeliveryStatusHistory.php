<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocalDeliveryStatusHistory extends Model
{
    protected $fillable = [
        'local_delivery_id',
        'old_status',
        'new_status',
        'changed_by',
        'changed_at',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function localDelivery(): BelongsTo
    {
        return $this->belongsTo(LocalDelivery::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
