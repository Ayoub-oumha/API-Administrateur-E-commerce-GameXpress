<?php

use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\RoleController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/sayHello', [AuthController::class, 'sayHello']);
Route::post('/v1/admin/register', [AuthController::class, 'register']);
Route::post('/v1/admin/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function() {
    Route::post('/v1/admin/logout', [AuthController::class, 'logout']);
});
// Admin routes
Route::middleware('auth:sanctum')->group(function() {
    Route::middleware('role:super_admin')->group(function() {
    
        Route::get('/v1/admin/roles', [RoleController::class, 'index']);
        Route::middleware('permission:view_dashboard')->group(function() {
            Route::get('/v1/admin/dashboard', [DashboardController::class, 'index']);
            Route::get('/v1/admin/dashboard/stock-alerts', [DashboardController::class, 'stockAlerts']);
        });
       
});
});
 


// test route
Route::get('/test', function() {
    return response()->json(['message' => 'Test route works!']);
});
