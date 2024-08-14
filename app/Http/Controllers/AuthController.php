<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed'],
        ]);

        $user = User::create($validated);
        $token = $user->createToken($validated['name']);

        return  response()->json([
            'user' => $user,
            'token' => $token->plainTextToken
        ]);
    }
    function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $validated['email'])->first();
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'the provided credentials are incorrect'
            ])->setStatusCode(404);
        }

        $token = $user->createToken($user->name);

        return  response()->json([
            'user' => $user,
            'token' => $token->plainTextToken
        ]);
    }
    function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'you are logged out'
        ]);
    }
}
