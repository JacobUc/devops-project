<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'full_name',
        'birth_date',
        'curp',
        'address',
        'monthly_salary',
        'license_number',
        'system_entry_date',
    ];
}
