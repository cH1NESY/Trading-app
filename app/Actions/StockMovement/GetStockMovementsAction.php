<?php

namespace App\Actions\StockMovement;

use App\DTO\StockMovementFilterDTO;
use App\Models\StockMovement;

class GetStockMovementsAction
{
    /**
     * Получить историю движений с фильтрами и пагинацией
     *
     * @param StockMovementFilterDTO $dto DTO с фильтрами
     * @param int $perPage Количество на страницу
     * @param int|null $page Номер страницы
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function execute(StockMovementFilterDTO $dto, int $perPage = 15, int $page = null)
    {
        // Формируем запрос с загрузкой связей и фильтрами
        $query = StockMovement::with(['product', 'warehouse'])
            ->when($dto->product_id, fn($q, $id) => $q->where('product_id', $id))
            ->when($dto->warehouse_id, fn($q, $id) => $q->where('warehouse_id', $id))
            ->when($dto->date_from, fn($q, $date) => $q->where('created_at', '>=', $date))
            ->when($dto->date_to, fn($q, $date) => $q->where('created_at', '<=', $date))
            ->latest();
        // Пагинация с учётом номера страницы
        if ($page !== null) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }
        // Стандартная пагинация
        return $query->paginate($perPage);
    }
} 