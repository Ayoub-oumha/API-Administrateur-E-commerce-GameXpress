<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call(RolePermissionSeeder::class);
        $this->call([
            // UserSeeder::class,     // If you have a UserSeeder
            CategorySeeder::class, // Category seeder
            ProductSeeder::class,  // Product seeder
        ]);
        // Create admin user
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gamexpress.com',
            'password' => Hash::make('password')
        ]);
        
        // Assign super_admin role
        $admin->assignRole('super_admin');
        
        // Create product manager user
        $productManager = User::create([
            'name' => 'Product Manager',
            'email' => 'products@gamexpress.com',
            'password' => Hash::make('password')
        ]);
        
        // Assign product_manager role
        $productManager->assignRole('product_manager');
        
        // Create user manager
        $userManager = User::create([
            'name' => 'User Manager',
            'email' => 'users@gamexpress.com',
            'password' => Hash::make('password')
        ]);
        
        // Assign user_manager role
        $userManager->assignRole('user_manager');
    }
}