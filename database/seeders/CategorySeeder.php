<?php

// filepath: c:\Users\lenovo\Desktop\Api laravel\API_Administrateur_E-commerce_(GameXpress)\database\seeders\CategorySeeder.php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Console Gaming',
            'PC Gaming',
            'Gaming Accessories',
            'Games',
            'PlayStation',
            'Xbox',
            'Nintendo',
            'Gaming Laptops',
            'Gaming Desktops',
            'PC Components',
            'Controllers',
            'Headsets',
            'Gaming Mice',
            'Gaming Keyboards',
            'Action Games',
            'Adventure Games',
            'RPG Games',
            'Sports Games',
        ];
    
        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }
    }
    
}