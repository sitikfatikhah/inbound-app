<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomsPayments extends Model
{
    protected $fillable = [
        'customs_clearance_id',
        'customs_value',
        'import_duty',
        'import_value',
        'vat',
        'pph_22_import',
        'luxury_tax',
        'other_taxes',
        'total_import_duty_and_taxes',
        'payment_date',
        'billing_code',
    ];
}
