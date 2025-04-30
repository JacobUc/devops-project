<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';
    protected $primaryKey = 'id_vehicle'; 
    
    protected $fillable = [
        'brand',
        'model',
        'vin',
        'plate_number',
        'purchase_date',
        'cost',
        'photo',
        'registration_date',
    ];
}
