<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'Champs invalides'], 422);
        }

        $creds = $request->only('email', 'password');
        if (!Auth::attempt($creds)) {
            return response()->json(['error' => 'Identifiants incorrects'], 401);
        }

        $user = $request->user()->load('role');
        // Révoque anciens tokens et crée un nouveau
        $user->tokens()->delete();
        $token = $user->createToken('web')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
        }
        return response()->json(['ok' => true]);
    }
}