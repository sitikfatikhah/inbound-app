<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'product_code',
        'product_name',
        'hs_code',
        'import_duty', // percentage
        'facility', // coo
        'description',
        'brand',
        'category',
        'unit',
        'country_of_origin',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
