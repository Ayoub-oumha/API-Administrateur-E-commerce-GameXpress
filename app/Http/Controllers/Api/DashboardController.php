<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Welcome to the dashboard',
        ]);
    }
    public function stockAlerts()
    {
        return response()->json([
            'message' => 'Stock alerts',
        ]);
    }
}
