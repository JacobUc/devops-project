<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Models\InvitationCode;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

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
                return response()->json([
                    'message' => 'No admins found',
                    'data' => null,
                    'status' => 200,
                ], 200);
            }

            return response()->json([
                'data' => AdminResource::collection($admins),
                'status' => 200,
            ], 200);

        }catch(\Exception $e){
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
                'password' => bcrypt($request->password),
                'id_invitation_code' => $invitationCode->id,
            ]);

            if(!$newAdmin){
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

            return response()->json([
                'data' => $adminResponseDTO,
                'status' => 201,
            ], 201);

        }catch(\Exception $e){
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
                return response()->json([
                    'message' => 'Admin not found',
                    'data' => null,
                    'status' => 404,
                ], 404);
            }

            return response()->json([
                'data' => new AdminResource($admin),
                'status' => 200,
            ], 200);
        }catch(\Exception $e){
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
                $admin->password = Hash::make($request->password);
            }

            $admin->save();

            $adminResponseDTO = [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email
            ];

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
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validateStatusRequest->errors() + $validateStatusId->errors(),
                    'status' => 400,
                ], 400);
            }

            $admin = Admin::find($id);
            if(!$admin) {
                return response()->json([
                    'message' => 'Admin not found',
                    'data' => null,
                    'status' => 404,
                ], 404);
            }

            if (!Hash::check($request->password, $admin->password)) {
                return response()->json([
                    'message' => 'Incorrect password',
                    'status' => 403,
                ], 403);
            }
            $admin->delete();

            return response()->json([
                'message' => 'Admin deleted successfully',
                'data' => null,
                'status' => 200,
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Error deleting admin',
                'data' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
