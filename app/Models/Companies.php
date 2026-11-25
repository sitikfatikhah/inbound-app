<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Companies extends Authenticatable
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
        'logo',
        'is_active',
    ];
}
