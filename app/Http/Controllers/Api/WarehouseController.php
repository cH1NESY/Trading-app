<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $warehouses = Warehouse::withCount('orders')->get();
        return response()->json(['success' => true, 'data' => $warehouses]);
    }

    public function getStocks(Warehouse $warehouse)
    {
        $stocks = $warehouse->stocks()->with('product')->get();
        return response()->json(['success' => true, 'data' => $stocks]);
    }
} 