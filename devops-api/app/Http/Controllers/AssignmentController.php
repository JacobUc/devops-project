<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    public function index()
    {
        return Assignment::all();
    }

    public function store(Request $request)
    {
        // Validaciones básicas
        $validator = Validator::make($request->all(), [
            'assignment_date' => 'required|date',
            'id_driver' => 'required|exists:drivers,id_driver',
            'id_vehicle' => 'required|exists:vehicles,id_vehicle',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Validar que el conductor y vehículo no estén asignados ya ese día
        $assignmentDate = $request->assignment_date;
        $idDriver = $request->id_driver;
        $idVehicle = $request->id_vehicle;

        $conflictDriver = Assignment::where('id_driver', $idDriver)
            ->where('assignment_date', $assignmentDate)
            ->exists();

        $conflictVehicle = Assignment::where('id_vehicle', $idVehicle)
            ->where('assignment_date', $assignmentDate)
            ->exists();

        if ($conflictDriver) {
            return response()->json(['error' => 'El conductor ya tiene una asignación para esa fecha.'], 409);
        }

        if ($conflictVehicle) {
            return response()->json(['error' => 'El vehículo ya tiene una asignación para esa fecha.'], 409);
        }

        // Crear la asignación
        $assignment = Assignment::create([
            'assignment_date' => $assignmentDate,
            'id_driver' => $idDriver,
            'id_vehicle' => $idVehicle,
        ]);

        return response()->json($assignment, 201);
    }

    public function show($id)
    {
        return Assignment::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $assignment = Assignment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'assignment_date' => 'required|date',
            'id_driver' => 'required|exists:drivers,id_driver',
            'id_vehicle' => 'required|exists:vehicles,id_vehicle',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $assignmentDate = $request->assignment_date;
        $idDriver = $request->id_driver;
        $idVehicle = $request->id_vehicle;

        // Validar conflictos (ignorando el propio ID que se está actualizando)
        $conflictDriver = Assignment::where('id_driver', $idDriver)
            ->where('assignment_date', $assignmentDate)
            ->where('id_assignment', '!=', $assignment->id_assignment)
            ->exists();

        $conflictVehicle = Assignment::where('id_vehicle', $idVehicle)
            ->where('assignment_date', $assignmentDate)
            ->where('id_assignment', '!=', $assignment->id_assignment)
            ->exists();

        if ($conflictDriver) {
            return response()->json(['error' => 'El conductor ya tiene una asignación para esa fecha.'], 409);
        }

        if ($conflictVehicle) {
            return response()->json(['error' => 'El vehículo ya tiene una asignación para esa fecha.'], 409);
        }

        // Actualizar asignación
        $assignment->update([
            'assignment_date' => $assignmentDate,
            'id_driver' => $idDriver,
            'id_vehicle' => $idVehicle,
        ]);

        return response()->json($assignment, 200);
    }

    public function destroy($id)
    {
        Assignment::destroy($id);

        return response()->json(null, 204);
    }
}

