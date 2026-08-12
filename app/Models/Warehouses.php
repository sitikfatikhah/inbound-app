<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouses extends Model
{
    protected $fillable = [
        'company_id',
        'warehouse_code',
        'warehouse_name',
        'country',
        'address',
        'contact_person',
        'email',
        'phone',
        'is_active',
    ];
}
