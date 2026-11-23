<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regulations extends Model
{
    protected $fillable = [
        'title',
        'category',
        'description',
        'document_file',
        'effective_date',
        'expiration_date',
        'notes',
        'is_active',
    ];
}
