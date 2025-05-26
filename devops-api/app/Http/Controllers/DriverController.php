<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function index(){
        $drivers = Driver::all(); 
        if ($drivers->isEmpty()) {
            $data = [
                'message' => 'No drivers found',
                'data' => null,
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        return response()->json($drivers,200);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'last_name' => 'required',
            'birth_date' => 'required|date',
            'curp' => 'required|unique:drivers,curp',
            'address' => 'required',
            'monthly_salary' => 'required',
            'license_number' => 'required|unique:drivers,license_number',
            'system_entry_date' => 'required|date'

        ]);
        if ($validator->fails()) {
            $data =[
                'message' => 'Data validation error',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }
        $driver = Driver::create([
            
            'name' => $request->name,
            'last_name' => $request->last_name,
            'birth_date' => $request->birth_date,
            'curp' => $request->curp,
            'address' => $request->address,
            'monthly_salary' => $request->monthly_salary,
            'license_number' => $request->license_number,
            'system_entry_date' => $request->system_entry_date
        ]);
        if(!$driver){
            $data = [
                'message' => 'error creating driver',
                'data' => null,
                'status' => 500
            ];
        }
        $data = [
            'driver' => $driver,
            'status' => 201,
            'message' => 'Driver created successfully'
        ];
        return response()->json($data, 201);
    }
    public function show($id){
        $driver = Driver::find($id);

        if(!$driver){
            $data = [
                'message' => 'no driver found',
                'data' => null,
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $data = [
            'driver' => $driver,
            'status' => 200,
            'message' => 'Driver retrieved successfully'
        ];
        return response()->json($data, 200);
    }
    public function destroy($id){
        $driver = Driver::find($id);

        if(!$driver){
            $data = [
                'message' => 'no driver found',
                'data' => null,
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $driver->delete();

        $data = [
            'message' => '$driver delete',
            'data' => null,
            'status' => 200
        ];
        return response()->json($data, 200);
    }
    public function update (Request $request, $id){
        $driver = Driver::find($id);

        if(!$driver){
            $data = [
                'message' => 'driver not found',
                'data'=> null,
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $validator = Validator::make($request->all(), [
        
            'name' => 'required',
            'last_name' => 'required',
            'birth_date' => 'required|date',
            'curp' => 'required|unique:drivers,curp,' . $driver->id_driver . ',id_driver',
            'address' => 'required',
            'monthly_salary' => 'required',
            'license_number' => 'required|unique:drivers,license_number,' . $driver->id_driver . ',id_driver',
            'system_entry_date' => 'required|date'
        ]);
        if ($validator->fails()) {
            $data =[
                'message' => 'error in data validation',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }
        
        $driver->name = $request->name;
        $driver->last_name = $request->last_name;
        $driver->birth_date =$request->birth_date;
        $driver->curp = $request->curp;
        $driver->address = $request->address;
        $driver->monthly_salary = $request->monthly_salary;
        $driver->license_number = $request->license_number;
        $driver->system_entry_date = $request->system_entry_date;

        $driver->save();

        $data =[
            'message' => 'Updated driver',
            'data' => $driver,
            'status' => 200
        ];
        return response()->json($data, 200);
    }
    public function updatePartial(Request $request, $id){
        $driver = Driver::find($id);

        if(!$driver){
            $data = [
                'message' => 'no found driver',
                'data' => null,
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $validator = Validator::make($request->all(), [
            
            'name' => 'sometimes',
            'last_name' => 'sometimes',
            'birth_date' => 'sometimes|date',
            'curp' => 'sometimes|unique:drivers,curp,' . $driver->id_driver . ',id_driver',
            'address' => 'sometimes',
            'monthly_salary' => 'sometimes',
            'license_number' => 'sometimes|unique:drivers,license_number,' . $driver->id_driver . ',id_driver',
            'system_entry_date' => 'sometimes|date'
        ]);
        if ($validator->fails()) {
            $data =[
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }
        
        if ($request->has('name')) {
            $driver->name = $request->name;
        }
        if ($request->has('last_name')) {
            $driver->last_name = $request->last_name;
        }
        if ($request->has('birth_date')) {
            $driver->birth_date= $request->birth_date;
        }
        if ($request->has('curp')) {
            $driver->curp = $request->curp;
        }
        if ($request->has('address')) {
            $driver->address = $request->address;
        }
        if ($request->has('monthly_salary')) {
            $driver->monthly_salary = $request->monthly_salary;
        }
        if ($request->has('license_number')) {
            $driver->license_number = $request->license_number;
        }
        if ($request->has('system_entry_date')) {
            $driver->system_entry_date = $request->system_entry_date;
        }
       
        $driver->save();

        $data =[
            'message' => 'Updated driver',
            'data' => $driver,
            'status' => 200
        ];
        return response()->json($data, 200);
    }
}
