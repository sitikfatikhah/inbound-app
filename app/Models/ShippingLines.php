<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingLines extends Model
{
    protected $fillable = [
        'shipping_line_name',
        'contact_person',
        'email',
        'phone',
        'phone',
        'is_active',
    ];
}
