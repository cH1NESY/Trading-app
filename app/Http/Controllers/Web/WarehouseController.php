<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    public function showList()
    {
        $warehouses = Warehouse::withCount('orders')->get();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $warehouses
            ]);
        }

        return view('warehouses.index', compact('warehouses'));
    }

    public function getStocks(Warehouse $warehouse)
    {
        $stocks = $warehouse->stocks()->with('product')->get();
        return response()->json([
            'success' => true,
            'data' => $stocks
        ]);
    }
} 