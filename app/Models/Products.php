<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'company_id',
        'product_code',
        'product_name',
        'hs_code',
        'import_duty', //percentage
        'facility', //coo
        'description',
        'brand',
        'category',
        'unit',
        'country_of_origin',
        'is_active',
    ];
}
