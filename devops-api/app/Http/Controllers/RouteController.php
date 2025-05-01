<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RouteController extends Controller
{
    public function index()
    {
        return response()->json(Route::all());
    }

    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'route_date' => 'required|date',
                'was_successful' => 'nullable|boolean',
                'problem_description' => 'nullable|string',
                'comments' => 'nullable|string',
                'start_latitude' => 'required|numeric|between:-90,90',
                'start_longitude' => 'required|numeric|between:-180,180',
                'end_latitude' => 'required|numeric|between:-90,90',
                'end_longitude' => 'required|numeric|between:-180,180',
                'id_assignment' =>'required|exists:assignments'
            ]);
            $route = Route::create($validated);
            return response()->json($route, 201);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'The data provided is not valid.',
                'errors' => $e->errors(),
            ], 400);
        }        
    }

    public function show($id)
    {
        $route = Route::find($id);
        if(!$route){
            $data = [
                'message' => 'Route not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        return response()->json($route);
    }

    public function update(Request $request, $id)
    {
        $route = Route::find($id);
        if(!$route){
            $data = [
                'message' => 'Route not found',
                'status' => 404
            ];

            return response()->json($data, 404);
        }
        
        try{
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'route_date' => 'required|date',
                'was_successful' => 'nullable|boolean',
                'problem_description' => 'nullable|string',
                'comments' => 'nullable|string',
                'start_latitude' => 'required|numeric|between:-90,90',
                'start_longitude' => 'required|numeric|between:-180,180',
                'end_latitude' => 'required|numeric|between:-90,90',
                'end_longitude' => 'required|numeric|between:-180,180',
            ]);
            
            $route->update($validated);
            return response()->json($route, 200);

        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'The data provided is not valid.',
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function destroy($id)
    {
        $route = Route::find($id);
        if(!$route){
            $data = [
                'message' => 'Route not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
        $route->delete();
        $data = [
            'message' => 'Route deleted',
            'status' => 200
        ];
        return response()->json($data, 200);
    }
}
