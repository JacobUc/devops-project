<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateVehicleRequest extends FormRequest
{


    public function rules()
    {
        return [
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'vin' => 'required|string|unique:vehicles',
            'plate_number' => 'required|string|unique:vehicles',
            'purchase_date' => 'nullable|date',
            'cost' => 'nullable|numeric',
            'photo' => 'nullable|mimes:jpeg,png,jpg,webp|max:10240',
        ];
    }
}