<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StockMovementService;
use App\DTO\StockMovementFilterDTO;

class StockMovementController extends Controller
{
    /**
     * Получить историю движения товара с фильтрами и пагинацией
     *
     * @param Request $request HTTP-запрос с фильтрами
     * @param StockMovementService $service Сервис для получения истории
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, StockMovementService $service)
    {
        // Формируем DTO из параметров запроса
        $dto = new StockMovementFilterDTO($request->all());
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page');
        // Получаем историю движений через сервис
        $movements = $service->getFilteredMovements($dto, $perPage, $page);
        return response()->json(['success' => true, 'data' => $movements]);
    }
} 