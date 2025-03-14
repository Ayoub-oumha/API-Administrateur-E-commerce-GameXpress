<?php


namespace App\Http\Controllers\Api\V1\Admin\products;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images']);
        $products = $query->paginate($request->per_page ?? 15);

        if ($products->isNotEmpty()) {
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

 
    public function store(ProductRequest $request)
    {
        $validated = $request->validated();
        
        
        $imagesData = $request->file('images') ?? [];
        unset($validated['images']);
        
       
        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
      
        $product = Product::create($validated);
        
       
        $this->handleProductImages($product, $imagesData, $request);
        
        return (new ProductResource($product->load(['category', 'images'])))
            ->additional([
                'message' => 'Product created successfully',
                'status' => 'success'
            ])
            ->response()
            ->setStatusCode(201);
    }
  
    public function show(string $id)
    {
        $product = Product::with(['category', 'images'])->findOrFail($id);
        
        return (new ProductResource($product))
            ->additional([
                'message' => 'Product retrieved successfully',
                'status' => 'success'
            ]);
    }

   
   
public function update(ProductRequest $request, string $id)
{
    $product = Product::findOrFail($id);
    $validated = $request->validated();
    
    
    $imagesData = $request->file('images') ?? [];
    unset($validated['images']);
    
    
    if (isset($validated['name']) && !isset($validated['slug'])) {
        $validated['slug'] = Str::slug($validated['name']);
    }
    
    
    $product->update($validated);
    
    
    if (!empty($imagesData)) {
        $this->handleProductImages($product, $imagesData, $request);
    }
    
    return (new ProductResource($product->load(['category', 'images'])))
        ->additional([
            'message' => 'Product updated successfully',
            'status' => 'success'
        ]);
}
  
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        
        return response()->json([
            'message' => 'Product deleted successfully',
            'status' => 'success'
        ]);
    }
    
 
    private function handleProductImages(Product $product, $imagesData, Request $request)
    {
       
        if (!empty($imagesData)) {
            
            $existingImages = $product->images()->get();
            foreach ($existingImages as $image) {
                
                if (Storage::exists('public/products/' . basename($image->image_url))) {
                    Storage::delete('public/products/' . basename($image->image_url));
                }
            }
            
            
            $product->images()->delete();
            
            
            foreach ($imagesData as $index => $imageFile) {
               
                $isPrimary = $request->input("is_primary.{$index}", false);
                
                
                $path = $imageFile->store('public/products');
                $url = Storage::url($path);
                
                
                $product->images()->create([
                    'image_url' => $url,
                    'is_primary' => $isPrimary == "true" || $isPrimary === true
                ]);
            }
            
            
            $this->ensureOnePrimaryImage($product);
        }
    }
    
 
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