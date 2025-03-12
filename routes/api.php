<?php

use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/sayHello', [AuthController::class, 'sayHello']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);
});
// Admin routes
Route::middleware('auth:sanctum')->group(function() {
    Route::middleware('role:super_admin')->prefix('admin')->group(function() {
    
        Route::get('/roles', [RoleController::class, 'index']);
       
});
});



// test route
Route::get('/test', function() {
    return response()->json(['message' => 'Test route works!']);
   
});
