<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrders extends Model
{
    protected $fillable = [
        'company_id',
        'po_number',
        'supplier_id',
        'po_date',
        'currency',
        'exchange_rate',
        'incoterm',
        'payment_term',
        'delivery_date',
        'arrival_date',
        'status',
    ];
}
