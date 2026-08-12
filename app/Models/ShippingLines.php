<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingLines extends Model
{
    protected $fillable = [
        'company_id',
        'shipping_line_name',
        'contact_person',
        'email',
        'phone',
        'website',
        'is_active',
    ];
}
