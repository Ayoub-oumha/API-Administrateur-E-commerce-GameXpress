<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test basic category creation
     */
    public function test_can_create_category_with_valid_data(): void
    {
        $categoryData = [
            'name' => 'Gaming Consoles',
        ];

        $category = Category::create($categoryData);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertDatabaseHas('categories', [
            'name' => 'Gaming Consoles',
            'slug' => 'gaming-consoles'
        ]);
        
        // Test slug was automatically generated
        $this->assertEquals('gaming-consoles', $category->slug);
    }

    /**
     * Test category creation with custom slug
     */
    public function test_can_create_category_with_custom_slug(): void
    {
        $category = Category::create([
            'name' => 'PC Gaming',
            'slug' => 'pc-games'
        ]);

        $this->assertEquals('pc-games', $category->slug);
        $this->assertDatabaseHas('categories', [
            'name' => 'PC Gaming',
            'slug' => 'pc-games'
        ]);
    }

    /**
     * Test updating a category
     */
    public function test_can_update_category(): void
    {
        $category = Category::create([
            'name' => 'Original Category'
        ]);
        
        $category->update([
            'name' => 'Updated Category'
        ]);
        
        $this->assertEquals('Updated Category', $category->name);
        $this->assertEquals('original-category', $category->slug); // Slug shouldn't change automatically
        
        // Explicitly update slug
        $category->update([
            'slug' => 'updated-category-slug'
        ]);
        
        $this->assertEquals('updated-category-slug', $category->slug);
    }

    /**
     * Test soft delete functionality if applicable
     * Note: This assumes your Category model uses SoftDeletes
     */
    // public function test_category_soft_delete(): void
    // {
    //     $category = Category::create([
    //         'name' => 'Temporary Category'
    //     ]);
        
    //     $categoryId = $category->id;
    //     $category->delete();
        
    //     // Category should not be found with normal query
    //     $this->assertNull(Category::find($categoryId));
        
    //     // If using soft deletes, category should be found with withTrashed
    //     // If not using soft deletes, comment out this line
    //     $this->assertNotNull(Category::withTrashed()->find($categoryId));
    // }

    /**
     * Test category relationship with products
     */
    public function test_category_has_products_relationship(): void
    {
        $category = Category::create([
            'name' => 'Games'
        ]);
        
        // Create a few products in this category
        Product::create([
            'name' => 'Call of Duty',
            'price' => 59.99,
            'stock' => 10,
            'status' => 'available',
            'category_id' => $category->id
        ]);
        
        Product::create([
            'name' => 'FIFA 2025',
            'price' => 69.99,
            'stock' => 15,
            'status' => 'available',
            'category_id' => $category->id
        ]);
        
        // Test the relationship
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $category->products);
        $this->assertCount(2, $category->products);
        $this->assertEquals('Call of Duty', $category->products[0]->name);
    }

    /**
     * Test finding a category by slug
     */
    public function test_can_find_category_by_slug(): void
    {
        Category::create([
            'name' => 'Gaming Laptops'
        ]);
        
        $category = Category::where('slug', 'gaming-laptops')->first();
        
        $this->assertNotNull($category);
        $this->assertEquals('Gaming Laptops', $category->name);
    }
    
    /**
     * Test category uniqueness constraint on slug
     */
    public function test_category_slug_must_be_unique(): void
    {
        // Create first category
        Category::create([
            'name' => 'First Category',
            'slug' => 'unique-slug'
        ]);
        
        // Try to create another with same slug
        try {
            Category::create([
                'name' => 'Second Category',
                'slug' => 'unique-slug'
            ]);
            
            // If we get here, uniqueness constraint failed
            $this->fail('Category was created with duplicate slug');
        } catch (\Exception $e) {
            // This is expected behavior
            $this->assertTrue(true);
        }
        
        // Make sure only one category with this slug exists
        $this->assertEquals(1, Category::where('slug', 'unique-slug')->count());
    }

    /**
     * Test creating multiple categories
     */
    // public function test_can_create_multiple_categories(): void
    // {
    //     $categoriesData = [
    //         ['name' => 'Console Games'],
    //         ['name' => 'PC Games'],
    //         ['name' => 'Mobile Games']
    //     ];

    //     foreach ($categoriesData as $categoryData) {
    //         Category::create($categoryData);
    //     }

    //     $this->assertEquals(3, Category::count());
    //     $this->assertDatabaseHas('categories', ['name' => 'PC Games']);
    // }
}