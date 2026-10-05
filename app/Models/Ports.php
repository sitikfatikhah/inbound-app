<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ports extends Model
{
    protected $fillable = [
        'port_name',
        'country',
        'port_code',
        'city',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
