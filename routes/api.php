<?php

use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/sayHello', [AuthController::class, 'sayHello']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::middleware('role:super_admin')->prefix('admin')->group(function() {
    // Gestion des rôles
    Route::get('/roles', [RoleController::class, 'index']);
});
Route::get('/test', function() {
    return response()->json(['message' => 'Test route works!']);
});