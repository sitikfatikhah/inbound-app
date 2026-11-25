<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomsClearanceResponses extends Model
{
    protected $fillable = [
        'customs_clearance_id',
        'customs_response_type_id',
        'document_number',
        'response_date',
        'action_required',
        'action_taken',
        'attachment_path',
        'remarks',
    ];

    protected $casts = [
        'response_date' => 'datetime',
    ];

    public function customsClearance(): BelongsTo
    {
        return $this->belongsTo(
            CustomsClearances::class,
            'customs_clearance_id'
        );
    }

    public function responseType(): BelongsTo
    {
        return $this->belongsTo(CustomsResponseType::class, 'customs_response_type_id');
    }
}
