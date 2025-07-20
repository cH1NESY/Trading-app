<?php

namespace App\Services;

use App\DTO\StockMovementFilterDTO;
use App\Models\StockMovement;

class StockMovementService
{
    /**
     * Получить историю движений с фильтрами и пагинацией
     */
    public function getFilteredMovements(StockMovementFilterDTO $dto, int $perPage = 15)
    {
        $query = StockMovement::with(['product', 'warehouse']);

        if ($dto->product_id) {
            $query->where('product_id', $dto->product_id);
        }
        if ($dto->warehouse_id) {
            $query->where('warehouse_id', $dto->warehouse_id);
        }
        if ($dto->date_from) {
            $query->where('created_at', '>=', $dto->date_from);
        }
        if ($dto->date_to) {
            $query->where('created_at', '<=', $dto->date_to);
        }

        return $query->latest()->paginate($perPage);
    }
} 