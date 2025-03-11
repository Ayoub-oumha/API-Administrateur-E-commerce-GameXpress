<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function sayHello()
    {
        return response()->json([
            'message' => 'Hello World!'
        ]);
    }
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
        
        $token = $user->createToken('auth_token')->plainTextToken;
       
        
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ], 201);
}
public function login(Request $request)
{
    $validatedData = $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    
    $user = User::where('email', $validatedData['email'])->first();

    
    if (!$user || !Hash::check($validatedData['password'], $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    // Revoke old tokens (optional)
    // $user->tokens()->delete();
    
    
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'User logged in successfully',
        'user' => $user,
        'token' => $token,
        'token_type' => 'Bearer'
    ]);
}
    public function logout(Request $request)
    {
        try {
            
            $request->user()->currentAccessToken()->delete();
            
            return response()->json([
                'message' => 'User logged out successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500); 
        }
    }
}