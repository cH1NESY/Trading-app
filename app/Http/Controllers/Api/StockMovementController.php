<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StockMovementService;
use App\DTO\StockMovementFilterDTO;

class StockMovementController extends Controller
{
    public function index(Request $request, StockMovementService $service)
    {
        $dto = new StockMovementFilterDTO($request->all());
        $perPage = $request->get('per_page', 15);
        $movements = $service->getFilteredMovements($dto, $perPage);
        return response()->json(['success' => true, 'data' => $movements]);
    }
} 