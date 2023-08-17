<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    function register(Request $request)
    {
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }
    function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            // Autenticación exitosa
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }
        //dd($user);
        $user = User::where('email', $request->email)->first();

        $token = $user->createToken('token')->plainTextToken;
        //dd($token);
        return response()->json([
            'status' => true,
            'message' => 'Logeo Exitoso',
            'token' => $token,
        ], 200);
    }
}
