<?php
namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Http\Requests\CreateVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;

class VehicleController extends Controller
{
    public function index()
    {
        return Vehicle::all();
    }

    public function store(CreateVehicleRequest $request)
    {
        $path = $request->file('photo')->store('photos', 'public'); 

        // Crear el vehiculo
        $vehicle = Vehicle::create([
            'brand' => $request->brand,
            'model' => $request->model,
            'vin' => $request->vin,
            'plate_number' => $request->plate_number,
            'purchase_date' => $request->purchase_date,
            'cost' => $request->cost,
            'photo' => $path,  
            'registration_date' => $request->registration_date,
        ]);

        return response()->json($vehicle, 201);
    }

    public function show($id)
    {
        return Vehicle::findOrFail($id);
    }

    public function update(UpdateVehicleRequest $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        if ($request->hasFile('photo')) {
            // eliminar la foto anterior y subir
            Storage::disk('public')->delete($vehicle->photo);
            $path = $request->file('photo')->store('photos', 'public');
            $vehicle->photo = $path;
        }

        $vehicle->update($request->validated());

        return response()->json($vehicle, 200);
    }

    public function destroy($id)
    {
        Vehicle::destroy($id);

        return response()->json(null, 204);
    }
}
