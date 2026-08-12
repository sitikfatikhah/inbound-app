<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forwarders extends Model
{
    protected $fillable = [
        'company_id',
        'forwarder_name',
        'contact_person',
        'email',
        'phone',
        'is_active',
    ];
}
