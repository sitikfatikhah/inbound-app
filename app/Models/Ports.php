<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ports extends Model
{
    protected $fillable = [
        'country',
        'port_code',
        'port_name',
        'city',
        'type',
        'is_active',
    ];
}
