<?php

namespace App\Http\Controllers;

use App\Models\InvitationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\LoggerService;

class InvitationCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $codigos = InvitationCode::all();
            LoggerService::info('Listado de códigos de invitación solicitado', [
                'total' => count($codigos),
            ]);

            if(!$codigos){  
                return response()->json([
                    'message' => 'No invitation codes found',
                    'data' => null,
                    'status' => 200,
                ], 200);
            }

            return response()->json([
                'message' => 'Invitation codes retrieved successfully',
                'data' => $codigos,
                'status' => 200,
            ], 200);

        }catch(\Exception $e){
            LoggerService::error('Error al obtener los códigos de invitación', [
                'mensaje' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error retrieving invitation codes',
                'data' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validateStatus = Validator::make($request->all(), [
                'code' => 'required|string|max:50|unique:invitation_codes,code',
                'expires_at' => 'required|date|after:now',
            ]);

            if($validateStatus->fails()) {
                LoggerService::error('Error de validación al crear código de invitación', [
                    'input' => $request->all(),
                    'errores' => $validateStatus->errors(),
                ]);

                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validateStatus->errors(),
                    'status' => 400,
                ], 400);
            }

            $newCode = InvitationCode::create([
                'code' => $request->code,
                'expires_at' => $request->expires_at,
                'used_status' => false,
                'created_at' => now(),
            ]);

            LoggerService::info('Código de invitación creado', [
                'id' => $newCode->id,
                'code' => $newCode->code,
            ]);

            if(!$newCode) {
                return response()->json([
                    'message' => 'Error creating invitation code',
                    'data' => null,
                    'status' => 500,
                ], 500);
            }

            return response()->json([
                'data' => $newCode,
                'status' => 201,
            ], 201);
        }catch(\Exception $e){
            
            LoggerService::error('Error al crear código de invitación', [
                'mensaje' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error creating invitation code',
                'data' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $invitationCode = InvitationCode::find($id);
            if(!$invitationCode) {
                LoggerService::error('Código de invitación no encontrado', ['id' => $id]);

                return response()->json([
                    'message' => 'Invitation code not found',
                    'data' => null,
                    'status' => 404,
                ], 404);
            }

             LoggerService::debug('Código de invitación consultado', [
                'id' => $invitationCode->id,
            ]);

            return response()->json([
                'data' => $invitationCode,
                'status' => 200,
            ], 200);
        }catch(\Exception $e){
             LoggerService::error('Error al consultar código de invitación', [
                'mensaje' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error retrieving invitation code',
                'data' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $invitationCode = InvitationCode::find($id);
            if(!$invitationCode) {
                LoggerService::error('Código de invitación no encontrado para actualizar', ['id' => $id]);

                return response()->json([
                    'message' => 'Invitation code not found',
                    'data' => null,
                    'status' => 404,
                ], 404);
            }

            $validateStatus = Validator::make($request->all(), [
                'code' => 'required|string|max:50|unique:invitation_codes,code',
                'expires_at' => 'required|date',
                'used_status' => 'required|boolean',
            ]);

            $invitationCode->code = $request->code;
            $invitationCode->expires_at = $request->expires_at;
            $invitationCode->used_status = $request->used_status;
            $invitationCode->save();

            if(!$invitationCode) {
                return response()->json([
                    'message' => 'Error updating invitation code',
                    'data' => null,
                    'status' => 500,
                ], 500);
            }

            LoggerService::info('Código de invitación actualizado', [
                'id' => $invitationCode->id,
                'nuevo_estado' => $invitationCode->toArray(),
            ]);

            return response()->json([
                'message' => 'Invitation code updated successfully',
                'data' => $invitationCode,
                'status' => 200,
            ], 200);
        }catch(\Exception $e){
            LoggerService::error('Error al actualizar código de invitación', [
                'mensaje' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error updating invitation code',
                'data' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $invitationCode = InvitationCode::find($id);
            if(!$invitationCode) {
                LoggerService::error('Código de invitación no encontrado para eliminar', ['id' => $id]);

                return response()->json([
                    'message' => 'Invitation code not found',
                    'data' => null,
                    'status' => 404,
                ], 404);
            }

            $invitationCode->delete();

             LoggerService::info('Código de invitación eliminado', ['id' => $id]);
            return response()->json([
                'message' => 'Invitation code deleted successfully',
                'data' => null,
                'status' => 200,
            ], 200);
        }catch(\Exception $e){
            LoggerService::error('Error al eliminar código de invitación', [
                'mensaje' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error deleting invitation code',
                'data' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
