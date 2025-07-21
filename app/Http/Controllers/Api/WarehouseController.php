<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;


class WarehouseController extends Controller
{
    /**
     * Получить список складов с количеством заказов
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Получаем склады с количеством заказов
        $warehouses = Warehouse::withCount('orders')->get();
        return response()->json(['success' => true, 'data' => $warehouses]);
    }
}
