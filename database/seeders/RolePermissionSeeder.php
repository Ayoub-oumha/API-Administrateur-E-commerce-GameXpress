<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        // Dashboard
        Permission::create(['name' => 'view_dashboard']);
        
        // Products
        Permission::create(['name' => 'view_products']);
        Permission::create(['name' => 'create_products']);
        Permission::create(['name' => 'edit_products']);
        Permission::create(['name' => 'delete_products']);
        
        // Categories
        Permission::create(['name' => 'view_categories']);
        Permission::create(['name' => 'create_categories']);
        Permission::create(['name' => 'edit_categories']);
        Permission::create(['name' => 'delete_categories']);
        
        // Users
        Permission::create(['name' => 'view_users']);
        Permission::create(['name' => 'create_users']);
        Permission::create(['name' => 'edit_users']);
        Permission::create(['name' => 'delete_users']);
        
        // Create roles and assign permissions
        
        // Super Admin role
        $superAdminRole = Role::create(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo(Permission::all());
        
        // Product Manager role
        $productManagerRole = Role::create(['name' => 'product_manager']);
        $productManagerRole->givePermissionTo([
            'view_dashboard',
            'view_products', 'create_products', 'edit_products', 'delete_products',
            'view_categories', 'create_categories', 'edit_categories', 'delete_categories',
        ]);
        
        // User Manager role
        $userManagerRole = Role::create(['name' => 'user_manager']);
        $userManagerRole->givePermissionTo([
            'view_dashboard',
            'view_users', 'create_users', 'edit_users', 'delete_users',
        ]);
    }
}