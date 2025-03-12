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
        // $validatedData = $request->validate([
        //     'name' => 'required',
        //     'email' => 'required',
        //     'password' => 'required',
        // ]);
        // Custom validation messages for required fields
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 6 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        
        if(User::where('email', $validatedData['email'])->exists()){
            return response()->json([
                'message' => 'Email already exists'
            ], 400);
        }

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
    if(!auth()->attempt($validatedData)){
        return response(['message'=>'Invalid credentials']);
    }
    // Find user by email
    $user = User::where('email', $validatedData['email'])->first();

    // Check if user exists and password is correct
    if (!$user || !Hash::check($validatedData['password'], $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
    
    // Create new token
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'User logged in successfully',
        'user' => $user,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions()->pluck('name'),
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