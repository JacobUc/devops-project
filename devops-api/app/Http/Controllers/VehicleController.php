<?php
namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Http\Requests\CreateVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class VehicleController extends Controller
{

    public function update(UpdateVehicleRequest $request, $id): JsonResponse
    {

        \Log::info('Datos recibidos en update:', $request->all());
        $vehicle = Vehicle::findOrFail($id);
        
        //Log::debug('Update.Vehicle.input', $request->all());
        //Log::debug('Update.Vehicle.before', $vehicle->toArray());
    
        $data = $request->only([
            'brand',
            'model',
            'vin',
            'plate_number',
            'purchase_date',
            'cost',
        ]);
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $data['photo'] = $path;
            Log::debug('Update.Vehicle.photo_path', ['path' => $path]);
        }
       // \Log::debug('Update.Vehicle.request_method', ['method' => request()->method()]);
       // \Log::debug('Update.Vehicle.content', request()->all());
        
        $vehicle->update($data);
    
        // ––> Loguea el estado después de save
       // Log::debug('Update.Vehicle.after', $vehicle->toArray());
    
        return response()->json([
            'before' => $vehicle->getOriginal(),   // valores antes del update
            'input'  => $request->all(),           // lo que tú mandaste
            'after'  => $vehicle->toArray(),       // valores guardados
        ], 200);
    }
    

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

    public function destroy($id)
    {
        Vehicle::destroy($id);

        return response()->json(null, 204);
    }
}
