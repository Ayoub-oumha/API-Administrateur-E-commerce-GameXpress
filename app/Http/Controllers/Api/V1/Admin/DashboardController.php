<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Count products, users, and categories
        $productCount = Product::count();
        $userCount = User::count();
        $categoryCount = Category::count();
        
        // Products with low stock (less than 5)
        $lowStockProducts = Product::where('stock', '<', 5)
            ->where('status', 'available')
            ->select('id', 'name', 'stock')
            ->get();
        
        // Recently registered users (last 7 days)
        $recentUsers = User::where('created_at', '>=', now()->subDays(7))
            ->select('id', 'name', 'email', 'created_at')
            ->get();
        
        // Products by category
        $productsByCategory = Category::withCount('products')
            ->get()
            ->pluck('products_count', 'name');
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => [
                    'total_products' => $productCount,
                    'total_users' => $userCount,
                    'total_categories' => $categoryCount,
                    'low_stock_count' => $lowStockProducts->count(),
                ],
                'low_stock_products' => $lowStockProducts,
                'recent_users' => $recentUsers,
                'products_by_category' => $productsByCategory,
            ]
        ]);
    }
    public function stockAlerts()
    {
        $criticalStockProducts = Product::where('stock', '<', 5)
            ->where('status', 'available')
            ->select('id', 'name', 'stock', 'category_id')
            ->with('category:id,name')
            ->get();
            
        return response()->json([
            'status' => 'success',
            'data' => [
                'critical_stock_products' => $criticalStockProducts,
                'count' => $criticalStockProducts->count(),
            ]
        ]);
    }
}
