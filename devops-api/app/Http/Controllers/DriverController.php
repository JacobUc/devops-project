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
                'message' => 'No se encontraron conductores',
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
            'curp' => 'required',
            'address' => 'required',
            'monthly_salary' => 'required',
            'license_number' => 'required',
            'system_entry_date' => 'required|date'

        ]);
        if ($validator->fails()) {
            $data =[
                'message' => 'Error en la validación de datos',
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
                'message' => 'Error al crear al conductor',
                'status' => 500
            ];
        }
        $data = [
            'driver' => $driver,
            'status' => 201
        ];
        return response()->json($data, 201);
    }
    public function show($id){
        $driver = Driver::find($id);

        if(!$driver){
            $data = [
                'message' => 'Conductor no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $data = [
            'driver' => $driver,
            'status' => 200
        ];
        return response()->json($data, 200);
    }
    public function destroy($id){
        $driver = Driver::find($id);

        if(!$driver){
            $data = [
                'message' => 'driver no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $driver->delete();

        $data = [
            'message' => '$driver delete',
            'status' => 200
        ];
        return response()->json($data, 200);
    }
    public function update (Request $request, $id){
        $driver = Driver::find($id);

        if(!$driver){
            $data = [
                'message' => 'driver no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $validator = Validator::make($request->all(), [
        
            'name' => 'required',
            'last_name' => 'required',
            'birth_date' => 'required|date',
            'curp' => 'required|unique:driver',
            'address' => 'required',
            'monthly_salary' => 'required',
            'license_number' => 'required|unique:driver',
            'system_entry_date' => 'required|date'

        ]);
        if ($validator->fails()) {
            $data =[
                'message' => 'Error en la validación de datos',
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
            'message' => 'driver-> actualizado',
            'student' => $driver,
            'status' => 200
        ];
        return response()->json($data, 200);
    }
    public function updatePartial(Request $request, $id){
        $driver = Driver::find($id);

        if(!$driver){
            $data = [
                'message' => 'driver no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $validator = Validator::make($request->all(), [
            
            'name' => '',
            'last_name' => '',
            'birth_date' => 'date',
            'curp' => 'unique:driver',
            'address' => '',
            'monthly_salary' => '',
            'license_number' => 'unique:driver',
            'system_entry_date' => 'date'
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
            $student->birth_date= $request->birth_date;
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
            'message' => 'driver actualizado',
            'driver' => $driver,
            'status' => 200
        ];
        return response()->json($data, 200);
    }
}
