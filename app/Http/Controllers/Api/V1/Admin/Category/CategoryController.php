<?php

namespace App\Http\Controllers\Api\V1\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $query = Category::query();
        
        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        
        // Include product count if requested
        if ($request->boolean('with_counts')) {
            $query->withCount('products');
        }
        
        // Order by name by default
        $query->orderBy($request->sort_by ?? 'name', $request->sort_direction ?? 'asc');
        
        $categories = $query->paginate($request->per_page ?? 15);
        
        return CategoryResource::collection($categories)
            ->additional([
                'message' => 'Categories retrieved successfully',
                'status' => 'success'
            ]);
    }
    
    /**
     * Store a newly created category.
     */
    public function store(CategoryRequest $request)
    {
        $validated = $request->validated();
        
        // Generate slug if not provided
        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Create category
        $category = Category::create($validated);
        
        return (new CategoryResource($category))
            ->additional([
                'message' => 'Category created successfully',
                'status' => 'success'
            ])
            ->response()
            ->setStatusCode(201);
    }
    
    /**
     * Display the specified category.
     */
    public function show(string $id)
    {
        $category = Category::withCount('products')->findOrFail($id);
        
        return (new CategoryResource($category))
            ->additional([
                'message' => 'Category retrieved successfully',
                'status' => 'success'
            ]);
    }
    
    /**
     * Update the specified category.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $category = Category::findOrFail($id);
        $validated = $request->validated();
        
        // Generate slug if name provided but no slug
        if (isset($validated['name']) && !isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Update category
        $category->update($validated);
        
        // Reload with product count
        $category->loadCount('products');
        
        return (new CategoryResource($category))
            ->additional([
                'message' => 'Category updated successfully',
                'status' => 'success'
            ]);
    }
    
    /**
     * Remove the specified category.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete a category with products',
                'status' => 'error'
            ], 422);
        }
        
        $category->delete();
        
        return response()->json([
            'message' => 'Category deleted successfully',
            'status' => 'success'
        ]);
    }
}