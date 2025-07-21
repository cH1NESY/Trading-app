<?php

namespace App\Actions\Order;

use App\DTO\OrderFilterDTO;
use App\Models\Order;

class GetOrdersAction
{
    /**
     * Получить список заказов с фильтрацией и пагинацией
     *
     * @param OrderFilterDTO $dto DTO с фильтрами
     * @param int $perPage Количество элементов на страницу
     * @param int|null $page Номер страницы (если не указан, используется стандартная пагинация)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function execute(OrderFilterDTO $dto, int $perPage = 15, int $page = null)
    {
        // Формируем запрос с загрузкой склада и товаров
        $query = Order::with(['warehouse', 'items.product'])
            ->when($dto->status, fn($q, $status) => $q->where('status', $status))
            ->when($dto->warehouse_id, fn($q, $id) => $q->where('warehouse_id', $id))
            ->when($dto->customer, fn($q, $customer) => $q->where('customer', 'like', "%$customer%"))
            ->latest();
        // Если явно передан номер страницы — используем его, если нет, то стандартная пагинация
        if ($page !== null) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }
        return $query->paginate($perPage);
    }
}
