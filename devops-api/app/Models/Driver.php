<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'name',
        'last_name',
        'birth_date',
        'curp',
        'address',
        'monthly_salary',
        'license_number',
        'system_entry_date',
    ];
}
