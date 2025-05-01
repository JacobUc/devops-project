<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Verificar que el admin existe
        $admin = Admin::where('email', $credentials['email'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Crear el token
        $token = $admin->createToken('API Token')->plainTextToken;

        return response()->json(['token' => $token], 200);

        // return response()->json(['message' => 'Unauthorized'], 401);
    }
}
