<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Services\LoggerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::all();

        LoggerService::info('Listado de rutas solicitado', [
            'total' => count($routes),
        ]);

        return response()->json($routes);
    }

    public function store(Request $request)
    {
        try {
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

            LoggerService::info('Ruta creada con éxito', [
                'id' => $route->id,
                'datos' => $route->toArray(),
            ]);

            return response()->json($route, 201);

        } catch (ValidationException $e) {
            LoggerService::error('Error de validación al crear ruta', [
                'errores' => $e->errors(),
                'input' => $request->all(),
            ]);

            return response()->json([
                'message' => 'The data provided is not valid.',
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function show($id)
    {
        $route = Route::find($id);

        if (!$route) {
            LoggerService::error('Ruta no encontrada al buscar', [
                'id' => $id,
            ]);

            return response()->json([
                'message' => 'Route not found',
                'status' => 404,
            ], 404);
        }

        LoggerService::debug('Ruta consultada con éxito', [
            'id' => $route->id,
        ]);

        return response()->json($route);
    }

    public function update(Request $request, $id)
    {
        $route = Route::find($id);

        if (!$route) {
            LoggerService::error('Ruta no encontrada al actualizar', [
                'id' => $id,
            ]);

            return response()->json([
                'message' => 'Route not found',
                'status' => 404,
            ], 404);
        }

        try {
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

            $before = $route->toArray();
            $route->update($validated);

            LoggerService::info('Ruta actualizada', [
                'id' => $route->id,
                'antes' => $before,
                'después' => $route->toArray(),
            ]);

            return response()->json($route, 200);

        } catch (ValidationException $e) {
            LoggerService::error('Error de validación al actualizar ruta', [
                'errores' => $e->errors(),
                'id' => $id,
                'input' => $request->all(),
            ]);

            return response()->json([
                'message' => 'The data provided is not valid.',
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function destroy($id)
    {
        $route = Route::find($id);

        if (!$route) {
            LoggerService::error('Ruta no encontrada al eliminar', [
                'id' => $id,
            ]);

            return response()->json([
                'message' => 'Route not found',
                'status' => 404,
            ], 404);
        }

        $route->delete();

        LoggerService::info('Ruta eliminada', [
            'id' => $id,
        ]);

        return response()->json([
            'message' => 'Route deleted',
            'status' => 200,
        ], 200);
    }
}
