<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Models\InvitationCode;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Services\LoggerService;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $admins = Admin::all();

            if(!$admins){
                LoggerService::warning('No se encontraron administradores');
                return response()->json([
                    'message' => 'No admins found',
                    'data' => null,
                    'status' => 200,
                ], 200);
            }

            LoggerService::info('Listado de administradores obtenido', [
            'total' => $admins->count(),
            ]);
            return response()->json([
                'data' => AdminResource::collection($admins),
                'status' => 200,
            ], 200);

        }catch(\Exception $e){
            LoggerService::error('Error al obtener administradores', [
            'error' => $e->getMessage(),
            ]);
            return response()->json([
                'message' => 'Error retrieving admins',
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
                'name' => 'required|string|max:50',
                'email' => 'required|email|max:100|unique:admins,email',
                'password' => 'required|string|min:8|max:20',
                'invitation_code' => 'required',
            ]);

            if($validateStatus->fails()) {
                LoggerService::error('Error de validación al crear administrador', [
                    'input' => $request->all(),
                    'errores' => $validateStatus->errors()
                ]);
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validateStatus->errors(),
                    'status' => 400,
                ], 400);
            }

            //verify if invitation code exists, is not used and is not expired
            $invitationCode = InvitationCode::where('code', $request->invitation_code)
                ->where('used_status', false)
                ->where('expires_at', '>', now())
                ->first();

            if(!$invitationCode){
                LoggerService::error('Código de invitación inválido o expirado', [
                    'code' => $request->invitation_code
                ]);
                return response()->json([
                    'message' => 'Invalid or expired invitation code',
                    'data' => null,
                    'status' => 404,
                ], 404);
            }

            //create admin
            $newAdmin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'id_invitation_code' => $invitationCode->id,
            ]);

            if(!$newAdmin){
                LoggerService::error('Error al crear administrador');
                return response()->json([
                    'message' => 'Error creating admin',
                    'data' => null,
                    'status' => 500,
                ], 500);
            }

            $invitationCode->used_status = true;
            $invitationCode->save();

            $adminResponseDTO = [
                'id' => $newAdmin->id,
                'name' => $newAdmin->name,
                'email' => $newAdmin->email,
            ];

            LoggerService::info('Administrador creado con éxito', $adminResponseDTO);
            return response()->json([
                'data' => $adminResponseDTO,
                'status' => 201,
            ], 201);

        }catch(\Exception $e){
            LoggerService::error('Error inesperado al crear administrador', [
                'mensaje' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error creating admin',
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
            $admin = Admin::find($id);

            if (!$admin) {
                LoggerService::info('No se encontraron administradores');
                return response()->json([
                    'message' => 'Admin not found',
                    'data' => null,
                    'status' => 404,
                ], 404);
            }

            LoggerService::info('Listado de administradores obtenido', [
                'total' => $admins->count()
            ]);
            return response()->json([
                'data' => new AdminResource($admin),
                'status' => 200,
            ], 200);
        }catch(\Exception $e){
            LoggerService::error('Error al obtener administradores', [
                'mensaje' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error retrieving admin',
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
            $admin = Admin::find($id);

            if (!$admin) {
                LoggerService::error('Administrador no encontrado para actualizar', ['id' => $id]);

                return response()->json([
                    'message' => 'Admin not found',
                    'data' => null,
                    'status' => 404,
                ], 404);
            }

            $request->validate([
                'name' => 'sometimes|string',
                'email' => 'sometimes|email|unique:admins,email,' . $id,
                'password' => 'sometimes|string|min:6',
            ]);

            // Actualizar campos si se proporcionan
            if ($request->has('name')) {
                $admin->name = $request->name;
            }

            if ($request->has('email')) {
                $admin->email = $request->email;
            }

            if ($request->has('password')) {
                $admin->password = $request->password;
            }

            $admin->save();

            $adminResponseDTO = [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => $admin->password,
            ];

            LoggerService::info('Administrador actualizado', $adminResponseDTO);
            return response()->json([
                'message' => 'Admin updated successfully',
                'data' => $adminResponseDTO,
                'status' => 200,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Error updating admin',
                'data' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try{
            $validateStatusRequest = Validator::make($request->all(), [
                'password' => 'required',
            ]);
            $validateStatusId = Validator::make(['id' => $id], [
                'id' => 'required|integer|exists:admins,id',
            ]);

            if($validateStatusRequest->fails() || $validateStatusId->fails()) {
                LoggerService::error('Error de validación al eliminar administrador', [
                    'errores' => $validateStatusRequest->errors() + $validateStatusId->errors()
                ]);

                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validateStatusRequest->errors() + $validateStatusId->errors(),
                    'status' => 400,
                ], 400);
            }

            $admin = Admin::find($id);
            if(!$admin) {
                LoggerService::error('Administrador no encontrado para eliminar', ['id' => $id]);

                return response()->json([
                    'message' => 'Admin not found',
                    'data' => null,
                    'status' => 404,
                ], 404);
            }

            if (!Hash::check($request->password, $admin->password)) {
                LoggerService::error('Contraseña incorrecta al intentar eliminar admin', [
                    'id' => $id,
                    'email' => $admin->email
                ]);

                return response()->json([
                    'message' => 'Incorrect password',
                    'status' => 403,
                ], 403);
            }
            $admin->delete();

            LoggerService::info('Administrador eliminado', ['id' => $id]);
            return response()->json([
                'message' => 'Admin deleted successfully',
                'data' => null,
                'status' => 200,
            ], 200);
        }catch(\Exception $e){
            LoggerService::error('Error inesperado al eliminar administrador', [
                'mensaje' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Error deleting admin',
                'data' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
