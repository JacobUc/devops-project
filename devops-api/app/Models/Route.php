<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    //
    protected $fillable = [
        'name',
        'route_date',
        'was_successful',
        'problem_description',
        'comments',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'id_assignment', 'id_assignment');
    }
}
