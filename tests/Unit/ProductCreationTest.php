<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCreationTest extends TestCase
{
    use RefreshDatabase;

    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
      
        $this->category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);
    }

    public function test_can_create_product_with_valid_data(): void
    {
        $productData = [
            'name' => 'PlayStation 5',
            'description' => 'Next-gen gaming console from Sony',
            'price' => 499.99,
            'stock' => 10,
            'status' => 'available',
            'category_id' => $this->category->id
        ];

        $product = Product::create($productData);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertDatabaseHas('products', [
            'name' => 'PlayStation 5',
            'price' => 499.99
        ]);
        

        $this->assertEquals('playstation-5', $product->slug);
    }

    public function test_can_create_product_with_custom_slug(): void
    {
        $product = Product::create([
            'name' => 'Xbox Series X',
            'slug' => 'xbox-next-gen',
            'price' => 499.99,
            'stock' => 15,
            'status' => 'available',
            'category_id' => $this->category->id
        ]);

        $this->assertEquals('xbox-next-gen', $product->slug);
    }

 
    public function test_can_create_product_with_images(): void
    {
        // Create a product
        $product = Product::create([
            'name' => 'Nintendo Switch',
            'price' => 299.99,
            'stock' => 20,
            'status' => 'available',
            'category_id' => $this->category->id
        ]);

        // Add images to the product
        $product->images()->createMany([
            [
                'image_url' => 'https://example.com/switch-1.jpg',
                'is_primary' => true
            ],
            [
                'image_url' => 'https://example.com/switch-2.jpg',
                'is_primary' => false
            ]
        ]);

        // Refresh the product to get the images
        $product->refresh();

        // Assert images were created and associated
        $this->assertCount(2, $product->images);
        $this->assertEquals('https://example.com/switch-1.jpg', $product->images[0]->image_url);
        $this->assertTrue($product->images[0]->is_primary);
        $this->assertFalse($product->images[1]->is_primary);
    }

 
    public function test_product_belongs_to_category(): void
    {
        $product = Product::create([
            'name' => 'DualSense Controller',
            'price' => 69.99,
            'stock' => 30,
            'status' => 'available',
            'category_id' => $this->category->id
        ]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals('Test Category', $product->category->name);
        $this->assertEquals($this->category->id, $product->category->id);
    }
    

    public function test_can_create_product_with_minimum_fields(): void
    {
        $product = Product::create([
            'name' => 'Minimum Product',
            'price' => 9.99,
            'stock' => 5,
            'status' => 'available',
            'category_id' => $this->category->id
        ]);
        
        $this->assertNotNull($product->id);
        $this->assertEquals('Minimum Product', $product->name);
        $this->assertEquals('minimum-product', $product->slug);
        $this->assertNull($product->description);
    }
    public function test_can_update_product(): void
{
    $product = Product::create([
        'name' => 'Original Name',
        'price' => 99.99,
        'stock' => 10,
        'status' => 'available',
        'category_id' => $this->category->id
    ]);
    
    $product->update([
        'name' => 'Updated Name',
        'price' => 89.99
    ]);
    
    $this->assertEquals('Updated Name', $product->name);
    $this->assertEquals(89.99, $product->price);
    $this->assertEquals('original-name', $product->slug); // Slug shouldn't change automatically on update
}
public function test_product_soft_delete(): void
{
    $product = Product::create([
        'name' => 'Deletable Product',
        'price' => 29.99,
        'stock' => 5,
        'status' => 'available',
        'category_id' => $this->category->id
    ]);
    
    $productId = $product->id;
    $product->delete();
    
    // Product should not be found with normal query
    $this->assertNull(Product::find($productId));
    
    // Product should be found with withTrashed
    $this->assertNotNull(Product::withTrashed()->find($productId));
}
    

    
}