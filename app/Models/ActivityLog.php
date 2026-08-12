<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'module',
        'model_type',
        'model_id',
        'action',
        'reference_no',
        'description',
        'old_values',
        'new_values',
        'browser',
        'platform',
        'ip_address',
        'url',
        'http_method',
        'session_id',
        'status',
    ];

      protected $casts = [

        'old_values'=>'array',

        'new_values'=>'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Companies::class);
    }
}
