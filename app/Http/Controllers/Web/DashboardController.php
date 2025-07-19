<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Warehouse;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_warehouses' => Warehouse::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'active_orders' => Order::where('status', 'active')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'canceled_orders' => Order::where('status', 'canceled')->count(),
        ];

        $recent_orders = Order::with(['warehouse', 'items.product'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_orders'));
    }
} 