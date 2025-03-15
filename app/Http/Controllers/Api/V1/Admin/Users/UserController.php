<?php

namespace App\Http\Controllers\Api\V1\Admin\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->has('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        // Order by fields
        $query->orderBy($request->sort_by ?? 'created_at', $request->sort_direction ?? 'desc');
        
        $users = $query->paginate($request->per_page ?? 15);
        
        return UserResource::collection($users)
            ->additional([
                'message' => 'Users retrieved successfully',
                'status' => 'success'
            ]);
    }
    
    /**
     * Store a newly created user.
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();
        
        // Hash password
        $validated['password'] = Hash::make($validated['password']);
        
        // Create user
        $user = User::create($validated);
        
        // Assign roles
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }
        
        return (new UserResource($user))
            ->additional([
                'message' => 'User created successfully',
                'status' => 'success'
            ])
            ->response()
            ->setStatusCode(201);
    }
    
    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $user = User::with('roles')->findOrFail($id);
        
        return (new UserResource($user))
            ->additional([
                'message' => 'User retrieved successfully',
                'status' => 'success'
            ]);
    }
    
    /**
     * Update the specified user.
     */
    public function update(UserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validated();
        
        // Hash password if provided
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }
        
        // Update user
        $user->update($validated);
        
        // Update roles if provided
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }
        
        return (new UserResource($user))
            ->additional([
                'message' => 'User updated successfully',
                'status' => 'success'
            ]);
    }
    
    /**
     * Remove the specified user.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'You cannot delete your own account',
                'status' => 'error'
            ], 422);
        }
        
        $user->delete();
        
        return response()->json([
            'message' => 'User deleted successfully',
            'status' => 'success'
        ]);
    }
}