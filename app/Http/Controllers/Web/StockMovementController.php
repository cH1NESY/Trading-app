<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Services\StockMovementService;
use App\DTO\StockMovementFilterDTO;

class StockMovementController extends Controller
{
    public function showList(Request $request, StockMovementService $service)
    {
        $dto = new StockMovementFilterDTO($request->all());
        $perPage = $request->get('per_page', 15);
        $movements = $service->getFilteredMovements($dto, $perPage);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $movements
            ]);
        }

        $products = Product::all();
        $warehouses = Warehouse::all();
        return view('stock_movements.index', compact('movements', 'products', 'warehouses'));
    }
} 