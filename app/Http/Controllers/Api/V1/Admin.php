<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Models\User;
use Illuminate\Http\Request;

class Admin extends Controller
{
    public function index()
    {
        return AdminResource::collection(User::all());
    }
}
