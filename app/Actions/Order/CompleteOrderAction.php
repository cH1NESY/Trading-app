<?php

namespace App\Actions\Order;

use App\Models\Order;

class CompleteOrderAction
{
    /**
     * Завершить заказ
     *
     * @param Order $order Заказ для завершения
     * @return Order
     * @throws \RuntimeException Если заказ нельзя завершить
     */
    public function execute(Order $order): Order
    {
        // Проверяем, можно ли завершить заказ
        if (!$order->canBeCompleted()) {
            throw new \RuntimeException('Заказ не может быть завершен');
        }
        // Обновляем статус и дату завершения
        $order->update([
            'status' => Order::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
        // Возвращаем заказ с подгруженными связями
        return $order->fresh(['warehouse', 'items.product']);
    }
} 