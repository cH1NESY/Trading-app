<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StockMovementService;
use App\DTO\StockMovementFilterDTO;
use App\Actions\StockMovement\GetStockMovementsAction;

class StockMovementController extends Controller
{
    /**
     * Получить историю движения товара с фильтрами и пагинацией
     *
     * @param Request $request HTTP-запрос с фильтрами
     * @param GetStockMovementsAction $action Экшен для получения истории
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, GetStockMovementsAction $action)
    {
        // Формируем DTO из параметров запроса
        $dto = new StockMovementFilterDTO($request->all());
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page');
        // Получаем историю движений через Action
        $movements = $action->execute($dto, $perPage, $page);
        return response()->json(['success' => true, 'data' => $movements]);
    }
} 