<?php


namespace App\Http\Controllers\Api\V1\Admin\products;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images']);

        // Apply filters if provided
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Sort options
        $sortField = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_direction === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortField, $sortDirection);

        $products = $query->paginate($request->per_page ?? 15);


        if ($products->count() > 0) {
            return ProductResource::collection($products)
                ->additional([
                    'message' => 'Products retrieved successfully',
                    'status' => 'success'
                ]);
        } else {
            return response()->json([
                'message' => 'No products found',
                'status' => 'success'
            ]);
        }
    }

    /**
     * Store a newly created product.
     */
    public function store(ProductRequest $request)
    {
        $validated = $request->validated();
        // //reutrn error of requermnt and other of validation error
        
        // return response()->json([
        //     'message' => 'Validation error',
        //     'status' => 'error',
        //     
        // ]);
        // Extract images data
        $imagesData = $validated['images'] ?? [];
        unset($validated['images']);
        
        // Generate slug if not provided
        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Create product
        $product = Product::create($validated);
        
        // Handle images
        $this->handleProductImages($product, $imagesData);
        
        return (new ProductResource($product->load(['category', 'images'])))
            ->additional([
                'message' => 'Product created successfully',
                'status' => 'success'
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified product.
     */
    public function show(string $id)
    {
        $product = Product::with(['category', 'images'])->findOrFail($id);
        
        return (new ProductResource($product))
            ->additional([
                'message' => 'Product retrieved successfully',
                'status' => 'success'
            ]);
    }

    /**
     * Update the specified product.
     */
    public function update(ProductRequest $request, string $id)
    {
        // return response()->json([ "name"=> "younes jm3 sofl"]) ;
            
        
        $product = Product::findOrFail($id);
        $validated = $request->validated();
        
        // Extract images data
        $imagesData = $validated['images'] ?? null;
        unset($validated['images']);
        
        // Generate slug if name changed and slug not provided
        if (isset($validated['name']) && !isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Update product
        $product->update($validated);
        
        // Handle images if provided
        if ($imagesData) {
            $this->handleProductImages($product, $imagesData);
        }
        
        return (new ProductResource($product->load(['category', 'images'])))
            ->additional([
                'message' => 'Product updated successfully',
                'status' => 'success'
            ]);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        
        return response()->json([
            'message' => 'Product deleted successfully',
            'status' => 'success'
        ]);
    }
    
    /**
     * Handle product images creation/update.
     */
    private function handleProductImages(Product $product, array $imagesData)
    {
        // If replacing all images
        if (!empty($imagesData)) {
            // Delete existing images
            $product->images()->delete();
            
            // Create new images
            foreach ($imagesData as $imageData) {
                $product->images()->create($imageData);
            }
            
            // Make sure only one image is primary
            $this->ensureOnePrimaryImage($product);
        }
    }
    
    /**
     * Ensure only one image is set as primary.
     */
    private function ensureOnePrimaryImage(Product $product)
    {
        $primaryImages = $product->images()->where('is_primary', true)->get();
        
        if ($primaryImages->count() > 1) {
            // Multiple primary images found, keep only the first one
            foreach ($primaryImages->skip(1) as $image) {
                $image->update(['is_primary' => false]);
            }
        } elseif ($primaryImages->count() === 0 && $product->images()->count() > 0) {
            // No primary image but images exist, set first as primary
            $product->images()->first()->update(['is_primary' => true]);
        }
    }
}