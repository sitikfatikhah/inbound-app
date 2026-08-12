<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductProjects extends Model
{
    protected $fillable = [
        'product_id',
        'project_name',
        'description',
        'start_date',
        'target_date',
        'pic',
        'progress',
        'shipment_id',
    ];
}
