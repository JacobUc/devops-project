<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Http\Requests\CreateVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use Illuminate\Http\JsonResponse;
use App\Services\LoggerService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class VehicleController extends Controller
{
    public function index()
    {
        try {
            $data = Vehicle::paginate(10);
            LoggerService::info('Listado de vehículos paginado solicitado', ['total' => $data->total()]);
            return $data;

        } catch (Exception $e) {
            LoggerService::error('Error inesperado en index de vehículos', [
                'mensaje' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function store(CreateVehicleRequest $request)
    {
        try {
            $path = $request->file('photo')->store('photos', 'public');

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

            LoggerService::info('Vehículo creado', [
                'id' => $vehicle->id,
                'datos' => $vehicle->toArray(),
            ]);

            return response()->json($vehicle, 201);

        } catch (ValidationException $e) {
            LoggerService::error('Error de validación al crear vehículo', [
                'errores' => $e->errors(),
                'input' => $request->all(),
            ]);
            return response()->json(['errors' => $e->errors()], 400);

        } catch (Exception $e) {
            LoggerService::error('Error inesperado al crear vehículo', [
                'mensaje' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            LoggerService::debug('Detalle de vehículo solicitado', [
                'id' => $id,
            ]);

            return response()->json([
                'vehicle' => $vehicle,
                'status' => 200,
            ], 200);

        } catch (ModelNotFoundException $e) {
            LoggerService::error('Vehículo no encontrado al consultar', ['id' => $id]);
            return response()->json(['message' => 'Vehículo no encontrado'], 404);

        } catch (Exception $e) {
            LoggerService::error('Error inesperado al consultar vehículo', ['mensaje' => $e->getMessage()]);
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function update(UpdateVehicleRequest $request, $id): JsonResponse
    {
        try {
            LoggerService::info('Intentando actualizar vehículo', [
                'id' => $id,
                'input' => $request->all(),
            ]);

            $vehicle = Vehicle::findOrFail($id);
            $beforeUpdate = $vehicle->toArray();

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
                LoggerService::debug('Foto actualizada', ['path' => $path]);
            }

            $vehicle->update($data);

            LoggerService::info('Vehículo actualizado con éxito', [
                'id' => $id,
                'before' => $beforeUpdate,
                'after' => $vehicle->toArray(),
            ]);

            return response()->json([
                'before' => $beforeUpdate,
                'input'  => $request->all(),
                'after'  => $vehicle->toArray(),
            ], 200);

        } catch (ModelNotFoundException $e) {
            LoggerService::error('Vehículo no encontrado al actualizar', ['id' => $id]);
            return response()->json(['message' => 'Vehículo no encontrado'], 404);

        } catch (ValidationException $e) {
            LoggerService::error('Error de validación al actualizar vehículo', [
                'errores' => $e->errors(),
                'input' => $request->all(),
            ]);
            return response()->json(['errors' => $e->errors()], 400);

        } catch (Exception $e) {
            LoggerService::error('Error inesperado al actualizar vehículo', [
                'mensaje' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->delete();

            LoggerService::info('Vehículo eliminado', ['id' => $id]);

            return response()->json(['message' => 'Vehículo eliminado'], 200);

        } catch (ModelNotFoundException $e) {
            LoggerService::error('Vehículo no encontrado al eliminar', ['id' => $id]);
            return response()->json(['message' => 'Vehículo no encontrado'], 404);

        } catch (Exception $e) {
            LoggerService::error('Error inesperado al eliminar vehículo', ['mensaje' => $e->getMessage()]);
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
}
