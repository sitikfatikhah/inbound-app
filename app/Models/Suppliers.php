<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    protected $fillable = [
        'company_id',
        'supplier_code',
        'supplier_name',
        'country',
        'address',
        'contact_person',
        'email',
        'phone',
        'incoterm_default',
        'currency',
        'is_active',
    ];
}
