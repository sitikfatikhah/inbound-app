<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forwarders extends Model
{
    protected $fillable = [
        'forwarder_name',
        'contact_person',
        'email',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
