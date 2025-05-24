<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Services\LoggerService;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::all();
    
        if ($assignments->isEmpty()) {
            LoggerService::info('No se encontraron asignaciones');
            return response()->json([
                'message' => 'No assignments available.',
                'data' => []
            ], 200);
        }
        LoggerService::info('Listado de asignaciones obtenido', [
            'total' => count($assignments)
        ]);
        return response()->json([
            'message' => 'Assignment list retrieved successfully.',
            'data' => $assignments
        ], 200);
    }
    

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assignment_date' => [
                'required',
                'date',
                'after_or_equal:' . date('Y-m-d'),
            ],
            'id_driver' => 'required|exists:drivers,id_driver',
            'id_vehicle' => 'required|exists:vehicles,id_vehicle',
        ]);
    
        if ($validator->fails()) {
            LoggerService::error('Error de validación al crear asignación', [
                'input' => $request->all(),
                'errores' => $validator->errors()
            ]);

            $data = [
                'message' => 'Validation errors',
                'status' => 422,
                'errors' => $validator->errors()
            ];
            return response()->json($data, 422);
        }
    
        $assignmentDate = $request->assignment_date;
        $idDriver = $request->id_driver;
        $idVehicle = $request->id_vehicle;
    
        // Validate conflicts
        $conflictDriver = Assignment::where('id_driver', $idDriver)
            ->where('assignment_date', $assignmentDate)
            ->exists();
    
        $conflictVehicle = Assignment::where('id_vehicle', $idVehicle)
            ->where('assignment_date', $assignmentDate)
            ->exists();
    
        if ($conflictDriver) {
            LoggerService::error('Conflicto: conductor ya asignado ese día', [
                'id_driver' => $idDriver,
                'fecha' => $assignmentDate
            ]);

            $data = [
                'message' => 'Driver already has an assignment for this date.',
                'status' => 409
            ];
            return response()->json($data, 409);
        }
    
        if ($conflictVehicle) {
            LoggerService::error('Conflicto: vehículo ya asignado ese día', [
                'id_vehicle' => $idVehicle,
                'fecha' => $assignmentDate
            ]);
            $data = [
                'message' => 'Vehicle already has an assignment for this date.',
                'status' => 409
            ];
            return response()->json($data, 409);
        }
    
        // Create the assignment
        $assignment = Assignment::create([
            'assignment_date' => $assignmentDate,
            'id_driver' => $idDriver,
            'id_vehicle' => $idVehicle,
        ]);
    
        $data = [
            'message' => 'Assignment created successfully',
            'status' => 201,
            'data' => $assignment
        ];
        LoggerService::info('Asignación creada', [
            'id' => $assignment->id_assignment,
            'data' => $assignment->toArray()
        ]);
        return response()->json($data, 201);
    }

    public function show($id)
    {
        $assignment = Assignment::find($id);
    
        if (!$assignment) {
            LoggerService::error('Asignación no encontrada', ['id' => $id]);
            return response()->json([
                'message' => 'Assignment not found'
            ], 404);
        }
    
        return response()->json([
            'message' => 'Assignment retrieved successfully',
            'data' => $assignment
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $assignment = Assignment::find($id);
    
        if (!$assignment) {
            LoggerService::error('Asignación no encontrada para actualizar', ['id' => $id]);
            $data = [
                'message' => 'Assignment not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
    
        // Validate that it is not a past assignment
        if (!empty($assignment->assignment_date) && strtotime($assignment->assignment_date) !== false) {
            if (strtotime($assignment->assignment_date) < strtotime(date('Y-m-d'))) {
                LoggerService::error('Intento de modificar asignación pasada', [
                'id' => $id,
                'fecha_asignada' => $assignment->assignment_date
            ]);
                $data = [
                    'message' => 'Cannot modify a past assignment',
                    'status' => 403
                ];
                return response()->json($data, 403);
            }
        } else {
            $data = [
                'message' => 'Invalid assignment date',
                'status' => 400
            ];
            return response()->json($data, 400);
        }
    
        // Basic validations
        $validator = Validator::make($request->all(), [
            'assignment_date' => [
                'required',
                'date',
                'after_or_equal:' . Carbon::today()->toDateString(),
            ],
            'id_driver' => 'required|exists:drivers,id_driver',
            'id_vehicle' => 'required|exists:vehicles,id_vehicle',
        ]);
    
        if ($validator->fails()) {
            LoggerService::error('Error de validación al actualizar asignación', [
                'input' => $request->all(),
                'errores' => $validator->errors()
            ]);
            $data = [
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
                'status' => 422
            ];
            return response()->json($data, 422);
        }
    
        $assignmentDate = $request->assignment_date;
        $idDriver = $request->id_driver;
        $idVehicle = $request->id_vehicle;
    
        // Validate conflicts (excluding current assignment)
        $conflictDriver = Assignment::where('id_driver', $idDriver)
            ->where('assignment_date', $assignmentDate)
            ->where('id_assignment', '!=', $assignment->id_assignment)
            ->exists();
    
        $conflictVehicle = Assignment::where('id_vehicle', $idVehicle)
            ->where('assignment_date', $assignmentDate)
            ->where('id_assignment', '!=', $assignment->id_assignment)
            ->exists();
    
        if ($conflictDriver) {
            LoggerService::error('Conflicto: conductor ya tiene otra asignación ese día', [
                'id_driver' => $idDriver,
                'fecha' => $assignmentDate
            ]);
            $data = [
                'message' => 'The driver already has an assignment for that date',
                'status' => 409
            ];
            return response()->json($data, 409);
        }
    
        if ($conflictVehicle) {
            LoggerService::error('Conflicto: vehículo ya tiene otra asignación ese día', [
                'id_vehicle' => $idVehicle,
                'fecha' => $assignmentDate
            ]);
            $data = [
                'message' => 'The vehicle already has an assignment for that date',
                'status' => 409
            ];
            return response()->json($data, 409);
        }
    
        // Update assignment
        $assignment->update([
            'assignment_date' => $assignmentDate,
            'id_driver' => $idDriver,
            'id_vehicle' => $idVehicle,
        ]);
    
        $data = [
            'message' => 'Assignment updated successfully',
            'assignment' => $assignment,
            'status' => 200
        ];
        LoggerService::info('Asignación actualizada', [
            'id' => $assignment->id_assignment,
            'data' => $assignment->toArray()
        ]);
        return response()->json($data, 200);
    }

    public function destroy($id)
    {
        $assignment = Assignment::find($id);
    
        if (!$assignment) {
            LoggerService::error('Asignación no encontrada para eliminar', ['id' => $id]);
            $data = [
                'message' => 'Assignment not found',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
    
        $assignment->delete();
    
        $data = [
            'message' => 'Assignment deleted',
            'status' => 200
        ];
        LoggerService::info('Asignación eliminada', ['id' => $id]);
        return response()->json($data, 200);
    }
    
}

