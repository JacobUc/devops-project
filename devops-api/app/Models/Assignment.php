<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $primaryKey = 'id_assignment';

    protected $fillable = [
        'assignment_date',
        'id_driver',
        'id_vehicle',
    ];

    // Relaciones
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'id_driver', 'id_driver');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'id_vehicle', 'id_vehicle');
    }

    public function route()
    {
        return $this->hasOne(Route::class, 'id_assignment');
    }
}
